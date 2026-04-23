<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:15:23
  from 'C:\TAOPIX\MediaAlbumWeb\Customise\\email\customer_orderconfirmation\email_orderfooter.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa62eb763260_25367084',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cc28543ea9cb2e76db77f7a5e90b45d09ab21b73' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Customise\\\\email\\customer_orderconfirmation\\email_orderfooter.tpl',
      1 => 1726154402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa62eb763260_25367084 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\external\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.count.php','function'=>'smarty_modifier_count',),));
if (smarty_modifier_count($_smarty_tpl->tpl_vars['orderfootersections']->value) > 0) {?>
<div class="orderFooter" id="orderFooter">
	<div class="headerbar">
        <div class="headertext"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalItems');?>
</div>
    </div>
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootersections']->value, 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 		<?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>
            <div style="padding: 10px;">
                <table width="600" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                            <td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding:10px;">
                                <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                            </td>
                            <td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="200">
                                <span class="txt_componentname">
                                    <?php if ($_smarty_tpl->tpl_vars['section']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['section']->value['prompt'] == '') {?>
                                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span>
                                        <span> - </span>
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
                            <td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="130">
                                <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                                    <div class="paymentcomponentquantity">
                                        <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding:10px;" width="130">
                                <span class="component-price">
                                    <span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
                                </span>
                            </td>
                        <?php } else { ?>
                            <td style="word-break:break-all; padding:10px;" width="295">
                                <span class="txt_componentname">
                                    <?php if ($_smarty_tpl->tpl_vars['section']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['section']->value['prompt'] == '') {?>
                                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span>
                                        <span> - </span>
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
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                                    <div class="paymentcomponentquantity" >
                                        <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td align="right" style="word-break:break-all; padding:10px;" width="130">
                                <span class="component-price">
                                    <span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
                                </span>
                            </td>
                        <?php }?>

                    </tr>
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        <td width="535" style="word-break:break-all;  padding: 10px;" colspan="3">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'] != '') {?>
                                <div class="section-info">
                                    <span class="txt_itemcomponentinfo">
                                        <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>

                                    </span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'] != '') {?>
                                <div class="section-info">
                                    <span class="txt_itemcomponentpriceinfo">
                                        <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>

                                    </span>
                                </div>
                            <?php }?>
                        </td>
                    </tr>
                </table>

            </div>

            <div>
                <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>
                    <span class="component-metadata"><?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>
</span>
                <?php }?>
            </div>


			<div class="line-total">
				                <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
					<?php if (($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0)) {?>
                    <div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
                    </div>
                    <div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span></span>
                    </div>
                    <?php }?>
                    <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span></span>
                            </div>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span></span>
                            </div>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span></span>
                            </div>
                        <?php } else { ?>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span></span>
                            </div>
                        <?php }?>
                    <?php } else { ?>
                        <div class="comp-sub-total">
                            <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                        </div>

                        						<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
							<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['section']->value['includesitemtaxtext'];?>
</span></div>
							<?php }?>
                        <?php }?>
                    <?php }?>
                <?php } else { ?>

                                        <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

						<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
								</div>
							<?php if (($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0)) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span></span>
								</div>

									                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span></span>
								</div>
								<?php }?>
							<?php }?>
														<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span></span>
								</div>
							<?php }?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span></span>
								</div>
						<?php } else { ?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span></span>
								</div>

														<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
								<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['section']->value['includesitemtaxtext'];?>
</span></div>
								<?php }?>
							<?php }?>
	                    <?php }?>

					<?php }?>

                                        <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
									</div>
                            <?php if (($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0)) {?>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span></span>
									</div>
																		<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span></span>
									</div>
									<?php }?>
                            <?php }?>
                                                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span></span>
									</div>
                            <?php }?>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span></span>
									</div>
                        <?php } else { ?>

														<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>

									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
									</div>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span></span>
									</div>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span></span>
									</div>
							<?php } else { ?>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span></span>
									</div>
							<?php }?>

                    	<?php }?>

                    <?php }?>

												<?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
							<div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
                            </div>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span></span>
                            </div>
                            <?php }?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span></span>
							</div>
						<?php }?>
                <?php }?>
			</div>
		<?php }?>

		<!-- sub-sections of order footer component start -->
		<div class="clear"></div>

		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?> 		<?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>
		<div style="padding: 10px;">
		<table width="600" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
			<tr style="color: #575757; font-family: Lucida Grande;">
				<?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
					<td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding:10px;">
						<img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
					</td>
					<td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="200">
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
					<td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="130">
						<?php if ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8) {?>
							<div class="paymentsubcomponentquantity">
								<span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span>
							</div>
						<?php }?>
					</td>
					<td bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding:10px;" width="130">
						<span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
					</td>
				<?php } else { ?>
					<td style="word-break:break-all; padding:10px;" width="295">
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
					<td align="right" style="word-break:break-all; padding:10px;" width="130">
						<span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
					</td>
				<?php }?>

			</tr>
			<tr style="color: #575757; font-family: Lucida Grande;">

				<td width="535" style="word-break:break-all;  padding: 10px;" colspan="3">

					<?php if (($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'] != '')) {?>
						<div style="background-color:white; width:100%; min-height:32px; word-wrap: break-word">
							<?php if ($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'] != '') {?>
								<div class="section-info">
									<span class="txt_itemsubcomponentinfo">
										<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>

									</span>
								</div>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'] != '') {?>
								<div class="section-info">
									<span class="txt_itemsubcomponentpriceinfo">
										<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>

									</span>
								</div>
							<?php }?>
						</div>
					<?php }?>
				</td>
			</tr>

			<tr style="color: #575757; font-family: Lucida Grande;">
				<td width="535" style="word-break:break-all;  padding: 10px;" colspan="3">
					<?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>
                        <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="component-metadata">
                            <span class="txt_subComponentMetaData">
                                <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>

                            </span>
                        </span>
                    <?php }?>
				</td>
			</tr>
		</table>
    </div>

			<div class="clear"></div>
			<div class="line-total">
								<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
					<?php if (($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0)) {?>
					<div class="comp-sub-total">
						<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
						<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
					</div>
					<div class="comp-sub-total">
						<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span></span>
						<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span></span>
					</div>
					<?php }?>
					<?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
												<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span></span>
							</div>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span></span>
							</div>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span></span>
							</div>
						<?php } else { ?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span></span>
							</div>
						<?php }?>
					<?php } else { ?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span></span>
						</div>

												<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
							<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total"><span class="txt_sub_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['includesitemtaxtext'];?>
</span></div>
							<?php }?>
						<?php }?>
					<?php }?>
				<?php } else { ?>

										<?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

						<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
							</div>
							<?php if (($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0)) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span></span>
								</div>
																<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span></span>
								</div>
								<?php }?>
							<?php }?>
														<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span></span>
								</div>
							<?php }?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span></span>
								</div>
						<?php } else { ?>

							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span></span>
							</div>

														<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
								<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total"><span class="txt_sub_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['includesitemtaxtext'];?>
</span></div>
								<?php }?>
							<?php }?>
						<?php }?>

					<?php }?>

										<?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

						<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
								</div>
							<?php if (($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0)) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span></span>
								</div>
																<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span></span>
								</div>
								<?php }?>
							<?php }?>
														<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span></span>
								</div>
							<?php }?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span></span>
								</div>
						<?php } else { ?>

														<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>

								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
								</div>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span></span>
								</div>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span></span>
								</div>
							<?php } else { ?>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span></span>
								</div>
							<?php }?>

						<?php }?>

					<?php }?>

										<?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                        <?php if ((($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
						<div class="comp-sub-total">
                            <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
                        </div>
                        <div class="comp-sub-total">
                            <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span></span>
                        </div>
                        <?php }?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span></span>
						</div>

					<?php }?>
				<?php }?>
			</div>
		<?php }?>

		<div class="clear"></div>

		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 		<!-- sub-sections of order footer component end -->

		<!-- checkboxes inside component start -->

		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?> 		<?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
			<?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
                <table width="600" class="orderfootersubsection" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                    <tr>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td width="75" align="left" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview" src="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'];?>
" height="65" width="75" />
                        </td>
                        <td width="200" align="left" style="word-break:break-all; padding: 10px;">
                            <span class="txt_subcomponentname">
                                <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span><span>  - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                            </span>
                        </td>
                        <td width="130" align="left" class="txt_subcomponentqty" style="word-break:break-all; padding: 10px;">
                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) {?>
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                        <td width="130" align="right" style="word-break:break-all; padding: 10px;">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php } else { ?>
                        <td width="295" align="left" style="word-break:break-all; padding: 10px;">

                            <span class="txt_subcomponentname">
                                <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span><span>  - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                            </span>
                        </td>
                        <td width="130" align="left" class="txt_subcomponentqty" style="word-break:break-all; padding: 10px;">
                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) {?>
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                        <td width="130" align="right" style="word-break:break-all; padding: 10px;">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
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
                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '') {?><div class="checkbox-info"><span class="txt_itemsubcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>
</span></div><?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?><div class="checkbox-info"><span class="txt_itemsubcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>
</span></div><?php }?>
                        </td>
                    </tr>
                    <?php }?>

                </table>

                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>
                    <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata oderfootersubmetadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>"><span class="txt_subComponentMetaData"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>
</span></span>
                <?php }?>

				<div class="line-total">
											<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
							<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
							<div class="comp-sub-total" style="padding: 10px;">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
							</div>
							<div class="comp-sub-total" style="padding: 10px;">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
							</div>
							<?php }?>
							<?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
																<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
									</div>
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
									</div>
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
									</div>
								<?php } else { ?>
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
									</div>
								<?php }?>
							<?php } else { ?>
								<div class="comp-sub-total" style="padding: 10px;">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
								</div>

																<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
									<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
										<div class="comp-sub-total" style="padding: 10px;"><span class="txt_sub_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</span></div>
									<?php }?>
								<?php }?>
							<?php }?>
						<?php } else { ?>

														<?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>


								<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
										</div>
									<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
										</div>
																				<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
										</div>
										<?php }?>
									<?php }?>
																		<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
										</div>
									<?php }?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
										</div>
								<?php } else { ?>

									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
										<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
									</div>

																		<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
										<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
										<div class="comp-sub-total" style="padding: 10px;"><span class="txt_sub_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</span></div>
										<?php }?>
									<?php }?>

								<?php }?>

							<?php }?>

														<?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

								<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
										</div>
									<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
										</div>
																				<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
										</div>
										<?php }?>
									<?php }?>
																		<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
										</div>
									<?php }?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
										</div>
								<?php } else { ?>

																		<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>

										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
										</div>
										<div class="comp-sub-total" style="padding: 10px; background-color: white;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
										</div>
										<div class="comp-sub-total" style="padding: 10px; background-color: white;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
										</div>

									<?php } else { ?>
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
											<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
										</div>
									<?php }?>

								<?php }?>
							<?php }?>

														<?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                                <?php if ((($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
								<div class="comp-sub-total" style="padding: 10px;">
                                    <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                    <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                                </div>
                                <div class="comp-sub-total" style="padding: 10px;">
                                    <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
                                    <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
                                </div>
                                <?php }?>
								<div class="comp-sub-total" style="padding: 10px;">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
									<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
								</div>
							<?php }?>
						<?php }?>
				</div>
			<?php }?>
		<?php }?>

	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 		<!-- checkboxes inside component end -->
</div>
<div class="clear"></div>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
<!-- orderfooter checkboxes start -->
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootercheckboxes']->value, 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
	<?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
        <table width="600" class="orderfootersection" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
            <tr>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                <td width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                    <img class="componentPreview" src="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'];?>
" height="65" width="75" />
                </td>
                <td width="200" align="left" style="word-break:break-all; padding: 10px;">
                    <span>
                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span> <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                    </span>
                </td>
                <td width="130" align="left" class="txt_componentqty" style="word-break:break-all; padding: 10px;">
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) {?>
                        <div class="paymentsubcomponentquantity">
                            <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                        </div>
                    <?php }?>
                </td>
                <td width="130" align="right" style="word-break:break-all; padding: 10px;">
                    <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                </td>
                <?php } else { ?>
                <td width="275" align="left" bgcolor="white" style="word-break:break-all; padding: 10px;">

                    <span>
                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span> <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                    </span>
                </td>
                <td width="130" align="left" class="txt_componentqty" style="word-break:break-all; padding: 10px;">
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) {?>
                        <div class="paymentsubcomponentquantity">
                            <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                        </div>
                    <?php }?>
                </td>
                <td width="150" align="right" style="word-break:break-all; padding: 10px;">
                    <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
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
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '') {?><div class="checkbox-info"><span class="txt_itemcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>
</span></div><?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?><div class="checkbox-info"><span class="txt_itemcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>
</span></div><?php }?>
                </td>
            </tr>
            <?php }?>
        </table>

        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'])) {?>
            <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata oderfootermetadata <?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>
</span>
        <?php }?>

		<div class="line-total">
						<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
				<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
				<div class="comp-sub-total">
					<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
					<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
				</div>
				<div class="comp-sub-total">
					<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
					<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
				</div>
				<?php }?>
				<?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
										<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
						</div>
					<?php } else { ?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
						</div>
					<?php }?>
				<?php } else { ?>
					<div class="comp-sub-total">
						<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
						<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
					</div>

										<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
						<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
							<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</span></div>
						<?php }?>
					<?php }?>
				<?php }?>
			<?php } else { ?>

								<?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

					<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
							</div>
						<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
							</div>
                                                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
							</div>
							<?php }?>
						<?php }?>

												<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
							</div>
						<?php }?>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
							</div>
					<?php } else { ?>

							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
							</div>

												<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
							<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
								<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</span></div>
							<?php }?>
						<?php }?>
					<?php }?>

				<?php }?>

								<?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

				<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
						</div>
					<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
						</div>
												<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
						</div>
						<?php }?>
					<?php }?>
										<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
						</div>
					<?php }?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
						</div>
				<?php } else { ?>

										<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>

						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
						</div>
					<?php } else { ?>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
							<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span></span>
						</div>
					<?php }?>
				<?php }?>
			<?php }?>
								<?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                    <?php if ((($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
					<div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                    </div>
                    <div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span></span>
                    </div>
                    <?php }?>
                    <div class="comp-sub-total">
						<span class="total-heading"><span class="txt_orderfooterlinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
						<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span></span>
					</div>
				<?php }?>
			<?php }?>
		</div>
	<?php }
}?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
<!-- order footer checkboxes end -->

<span class="txt_linebreak"></span>
	<div class="orderfooter-sub-total">
		<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false)) {?>
			<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0)))) {?>
				<?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
					<span class="comp-sub-total">
						<span class="total-heading"><span class="txt_orderfooterfinaltotal"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</span></span>
						<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotal']->value;?>
</span></span>
					</span>
				<?php }?>
				<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
					<?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
						<?php if (($_smarty_tpl->tpl_vars['footertaxratesequal']->value == 1)) {?>
							<span class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterfinaltotal"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['orderfootertaxrate']->value;?>
%):</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxtotal']->value;?>
</span></span>
							</span>
						<?php } else { ?>
							<span class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterfinaltotal"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxname']->value;?>
:</span></span>
								<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxtotal']->value;?>
</span></span>
							</span>
						<?php }?>
					<?php }?>
				<?php }?>
			<?php }?>
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0))) && ($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
			<span class="comp-sub-total">
				<span class="total-heading"><span class="txt_orderfooterfinaltotal"><?php echo $_smarty_tpl->tpl_vars['orderfootertotalname']->value;?>
:</span></span>
				<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderfootertotal']->value;?>
</span></span>
			</span>
		<?php } else { ?>
			<span class="comp-sub-total">
				<span class="total-heading"><span class="txt_orderfooterfinaltotal"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</span></span>
				<span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotal']->value;?>
</span></span>
			</span>
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
			<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0)))) {?>
				<?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
					<?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
						<div class="comp-sub-total">
							<span class="txt_includetaxtextorderfooterfinal"><?php echo $_smarty_tpl->tpl_vars['includesorderfootertaxtext']->value;?>
</span>
						</div>
					<?php }?>
				<?php }?>
			<?php }?>
		<?php }?>

	</div>
</div>
<?php }?>

<div class="clear"></div><?php }
}
