<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:35:15
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\\ubbpro\email\customer_orderconfirmation3\email_jobticket.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb533901c0_37684509',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a2c81fcfb28937b0bb79df6369ce6918aabdc83' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\\\ubbpro\\email\\customer_orderconfirmation3\\email_jobticket.tpl',
      1 => 1653710166,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb533901c0_37684509 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="orderContent" class="content">

	<?php if ($_smarty_tpl->tpl_vars['hidepayments']->value != true) {?>

		<div id="paymenttableobj" name="paymenttableobj">
			<div class="headerbar"><div class="headertext"><span class="txt_sectionHeader"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPaymentMethod');?>
</span></div></div>
			<div class="selectedpaymentmethod"><span class='txt_paymentmethod'><?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value;?>
</span></div>
			<div class="paymentInfo"><?php echo $_smarty_tpl->tpl_vars['bankTransferInfo']->value;?>
</div>
		</div>

	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['metadatalayout']->value != '') {?>

		<div id="metadatatableobj">
			<?php echo $_smarty_tpl->tpl_vars['metadatalayout']->value;?>

		</div>

	<?php }?>

		<div class="headerbar"><div class="headertext"><span class="txt_sectionHeader"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSummary');?>
</span></div></div>

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

		<?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['orderfooter']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>



					<div class="shipping-line-total">
						<div class="line-sub-total">
							<span class="total-heading">
								<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShipping');?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingmethodname']->value;?>
):</span>
							</span>
							<span class="order-line-price">
								<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordertotalshipping']->value;?>
</span>
							</span>
						</div>

						<?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ((($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)) || ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true)))) {?>

            				<?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING')) {?>
					<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
						<div class="line-sub-total">
							<span class="total-heading">
								<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:</span>
							</span>
							<span class="order-line-price">
								<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>
</span>
							</span>
						</div>
					<?php }?>
										<?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

												<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
							<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>
</span>
									</span>
								</div>
							<?php }?>
								<div class="line-sub-total">
									<span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>
</span>
								</div>

						<?php } else { ?>
							<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>
</span>
									</span>
								</div>

							<?php }?>

								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>
</span>
									</span>
								</div>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>
</span>
									</span>
								</div>

			            <?php }?>

			        <?php } else { ?>
						<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
							<div class="line-sub-total">
								<span class="total-heading">
									<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
								</span>
								<span class="order-line-price">
									<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>
</span>
								</span>
							</div>
						<?php }?>
        			<?php }?>
   				<?php }?>

								<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)) || ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true))) {?>
						<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
							<div class="line-sub-total">
								<span class="total-heading">
									<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:</span>
								</span>
								<span class="order-line-price">
									<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>
</span>
								</span>
							</div>
						<?php }?>

										<?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

												<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
							<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>
</span>
									</span>
								</div>
							<?php }?>
								<div class="line-sub-total">
									<span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>
</span>
								</div>

						<?php } else { ?>
				            <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>
</span>
									</span>
								</div>
							<?php }?>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>
</span>
									</span>
								</div>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>
</span>
									</span>
								</div>
						<?php }?>

					<?php } else { ?>

						<?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>

							<div class="line-sub-total">
								<span class="total-heading">
									<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
								</span>
								<span class="order-line-price">
									<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>
</span>
								</span>
							</div>

						<?php }?>

			        <?php }?>

			    <?php }?>

			<?php } else { ?>

    			<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>

			        <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>
</span>
									</span>
								</div>
						<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>
</span>
										</span>
									</div>

						<?php } else { ?>

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>
</span>
										</span>
									</div>

						<?php }?>

					<?php }?>

										<?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

												<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
									<div class="line-sub-total">
										<span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>
</span>
									</div>
						<?php } else { ?>
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>
</span>
										</span>
									</div>
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>
</span>
										</span>
									</div>
						<?php }?>

					<?php }?>
				<?php } else { ?>

										<?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

												<?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

									<div class="line-sub-total">
										<span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>
</span>
									</div>

						<?php } else { ?>

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>
</span>
										</span>
									</div>
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>
</span>
										</span>
									</div>

						<?php }?>

					<?php }?>

				<?php }?>

			<?php }?>
</div>

		<div class="final-order-summary">
				<span class="txt_linebreak"></span>
				<span class="txt_singleLineDivider"></span>

					<?php if (((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false)) || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>

						<?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == false))) {?>

								
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderbeforediscounttotalvalue']->value;?>
</span>
										</span>
									</div>

								
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading"><?php echo $_smarty_tpl->tpl_vars['orderaftertotaldiscountname']->value;?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordertotaldiscountvalue']->value;?>
</span>
										</span>
									</div>
                        <?php } else { ?>
                            <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true))) {?>
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordersubtotal']->value;?>
</span>
										</span>
									</div>
                            <?php }?>
						<?php }?>

					<?php } else { ?>

						<?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['hastotaltax']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

								
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordersubtotal']->value;?>
</span>
										</span>
									</div>

						<?php }?>

					<?php }?>

							<?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['hastotaltax']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

								
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading"><?php echo $_smarty_tpl->tpl_vars['itemtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['itemtaxrate']->value;?>
%):</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordertotaltax']->value;?>
</span>
										</span>
									</div>
							<?php }?>


							<?php if (($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value > 0)) {?>

								
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderTotal');?>
:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>
</span>
										</span>
									</div>

								
								<?php if (($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value > 0 && $_smarty_tpl->tpl_vars['disabled_giftcard']->value == '')) {?>

							        <?php if ((($_smarty_tpl->tpl_vars['showalwaystaxtotal']->value == true) || ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

										<div class="line-sub-total">
											<span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['includestaxtotaltext']->value;?>
</span>
										</div>

									<?php }?>

										<div id="giftcard" class="line-sub-total <?php echo $_smarty_tpl->tpl_vars['disabled_giftcard']->value;?>
">
											<span class="total-heading">
												<span class="txt_total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCard');?>
:</span>
											</span>
											<span class="order-line-price">
												<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordergiftcardtotalvalue']->value;?>
</span>
											</span>
										</div>
										<div id="giftcard-remain" class="line-sub-total <?php echo $_smarty_tpl->tpl_vars['disabled_giftcard']->value;?>
">
											<span class="total-heading">
												<span class="txt_total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardRemaining');?>
:</span>
											</span>
											<span class="order-line-price" id="giftcardbalance">
												<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['giftcardbalance']->value;?>
</span>
											</span>
										</div>

								<?php }?>

							<?php }?>

								
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_total-heading"><?php echo $_smarty_tpl->tpl_vars['labelamounttopay']->value;?>
:</span>
									</span>
									<span class="order-line-price" id="ordertotaltopayvalue">
										<span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['ordertotaltopayvalue']->value;?>
</span>
									</span>
								</div>

							<?php if (($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value == 0 || $_smarty_tpl->tpl_vars['disabled_giftcard']->value == 'disabled')) {?>

								<?php if ((($_smarty_tpl->tpl_vars['showalwaystaxtotal']->value == true) || ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

									<div  class="line-sub-total" id="includetaxtextwithoutgiftcard">
										<span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['includestaxtotaltext']->value;?>
</span>
									</div>

								<?php }?>

							<?php }?>

		</div>	<!-- End order-summary -->

				<div class="headerbar"><div class="headertext"><span class="txt_sectionHeader"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingTitle');?>
</span></div></div>

					
					<div class="shippingaddress_container">
						<span class="txt_shippingaddress">
							<span class="label_shippingaddress"><?php echo $_smarty_tpl->tpl_vars['shippingStoreAddressLabel']->value;?>
: </span> <span class="txt_linebreak"><?php echo $_smarty_tpl->tpl_vars['shippingaddress']->value;?>
</span>
						</span>
					</div>
					<div class="billingaddress_container">
						<span class="txt_billingaddress">
							<span class="label_billingaddress"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBillingAddress');?>
: </span> <span class="txt_linebreak"><?php echo $_smarty_tpl->tpl_vars['billingaddress']->value;?>
</span>
						</span>
					</div>

					<span class="txt_linebreak"></span>

</div> <!-- content -->
<?php }
}
