<div id="orderContent" class="content">

	{if $hidepayments != true}

		<div id="paymenttableobj" name="paymenttableobj">
			<div class="headerbar"><div class="headertext"><span class="txt_sectionHeader">{#str_LabelPaymentMethod#}</span></div></div>
			<div class="selectedpaymentmethod"><span class='txt_paymentmethod'>{$paymentmethodslist}</span></div>
			<div class="paymentInfo">{$bankTransferInfo}</div>

		</div>

	{/if}

	{if $metadatalayout!=''}

		<div id="metadatatableobj">
			{$metadatalayout}
		</div>

	{/if}

		<div class="headerbar"><div class="headertext"><span class="txt_sectionHeader">{#str_LabelOrderSummary#}</span></div></div>

		{foreach from=$orderitems item=orderitem name=orderItemsLoop}
			{include file="$orderline" orderline=$orderitem}
		{/foreach}

		{include file="$orderfooter"}



					<div class="shipping-line-total">
						<div class="line-sub-total">
							<span class="total-heading">
								<span class="txt_shippinglinetotal">{#str_LabelOrderShipping#} ({$shippingmethodname}):</span>
							</span>
							<span class="order-line-price">
								<span class="txt_price">{$ordertotalshipping}</span>
							</span>
						</div>

			{ * VOUCHER * }
			{if ($vouchersection=='SHIPPING')||(($vouchersection=='TOTAL') && ((($differenttaxrates==true)&&(!$specialvouchertype)) || ($applyVoucherAsLineDiscount == true)))}

				{ * SHIPPING VOUCHER * }
				{if ($vouchersection=='SHIPPING')}
					{if ($shippingdiscountvalueraw > 0)}
						<div class="line-sub-total">
							<span class="total-heading">
								<span class="txt_shippinglinetotal">{$shippingdiscountname}:</span>
							</span>
							<span class="order-line-price">
								<span class="txt_price">{$shippingdiscountvalue}</span>
							</span>
						</div>
					{/if}
					{ * SHOW SHIPPING TAX * }
					{if ($showshippingtax)}

						{ * SHOW PRICES WITH TAX * }
						{if ($showpriceswithtax)}
							{if ($shippingdiscountvalueraw > 0)}
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingdiscountedvalue}</span>
									</span>
								</div>
							{/if}
								<div class="line-sub-total">
									<span class="txt_includetaxtext">{$includesshippingtaxtext}</span>
								</div>

						{else}
							{if ($shippingdiscountvalueraw > 0)}
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{#str_LabelSubTotal#}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingdiscountedvalue}</span>
									</span>
								</div>

							{/if}

								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{$shippingtaxname} ({$shippingtaxrate}%):</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingtaxtotal}</span>
									</span>
								</div>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingtotal}</span>
									</span>
								</div>

			            {/if}

			        {else}
						{if ($shippingdiscountvalueraw > 0)}
							<div class="line-sub-total">
								<span class="total-heading">
									<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
								</span>
								<span class="order-line-price">
									<span class="txt_price">{$shippingtotal}</span>
								</span>
							</div>
						{/if}
        			{/if}
   				{/if}

				{ * TOTAL VOUCHER * }
				{if (($vouchersection=='TOTAL')&& (($differenttaxrates==true)&&(!$specialvouchertype)) || ($applyVoucherAsLineDiscount == true))}
						{if ($shippingdiscountvalueraw > 0)}
							<div class="line-sub-total">
								<span class="total-heading">
									<span class="txt_shippinglinetotal">{$shippingdiscountname}:</span>
								</span>
								<span class="order-line-price">
									<span class="txt_price">{$shippingdiscountvalue}</span>
								</span>
							</div>
						{/if}

					{ * SHOW SHIPPING TAX * }
					{if ($showshippingtax)}

						{ * SHOW PRICES WITH TAX * }
						{if ($showpriceswithtax)}
							{if ($shippingdiscountvalueraw > 0)}
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingdiscountedvalue}</span>
									</span>
								</div>
							{/if}
								<div class="line-sub-total">
									<span class="txt_includetaxtext">{$includesshippingtaxtext}</span>
								</div>

						{else}
				            {if ($shippingdiscountvalueraw > 0)}
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{#str_LabelSubTotal#}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingdiscountedvalue}</span>
									</span>
								</div>
							{/if}
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{$shippingtaxname} ({$shippingtaxrate}%):</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingtaxtotal}</span>
									</span>
								</div>
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingtotal}</span>
									</span>
								</div>
						{/if}

					{else}

						{if ($shippingdiscountvalueraw > 0)}

							<div class="line-sub-total">
								<span class="total-heading">
									<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
								</span>
								<span class="order-line-price">
									<span class="txt_price">{$shippingtotal}</span>
								</span>
							</div>

						{/if}

			        {/if}

			    {/if}

			{else}

    			{if (($vouchersection=='TOTAL') && ($differenttaxrates==true) && ($specialvouchertype))}

			        {if ($shippingdiscountvalueraw > 0)}
								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_shippinglinetotal">{$shippingdiscountname}:</span>
									</span>
									<span class="order-line-price">
										<span class="txt_price">{$shippingdiscountvalue}</span>
									</span>
								</div>
						{if ($showpriceswithtax)}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$shippingdiscountedvalue}</span>
										</span>
									</div>

						{else}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal">{#str_LabelSubTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$shippingdiscountedvalue}</span>
										</span>
									</div>

						{/if}

					{/if}

					{ * SHOW SHIPPING TAX * }
					{if ($showshippingtax)}

						{ * SHOW PRICES WITH TAX * }
						{if ($showpriceswithtax)}
									<div class="line-sub-total">
										<span class="txt_includetaxtext">{$includesshippingtaxtext}</span>
									</div>
						{else}
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal">{$shippingtaxname} ({$shippingtaxrate}%):</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$shippingtaxtotal}</span>
										</span>
									</div>
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$shippingtotal}</span>
										</span>
									</div>
						{/if}

					{/if}
				{else}

					{ * SHOW SHIPPING TAX * }
					{if ($showshippingtax)}

						{ * SHOW PRICES WITH TAX * }
						{if ($showpriceswithtax)}

									<div class="line-sub-total">
										<span class="txt_includetaxtext">{$includesshippingtaxtext}</span>
									</div>

						{else}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal">{$shippingtaxname} ({$shippingtaxrate}%):</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$shippingtaxtotal}</span>
										</span>
									</div>
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_shippinglinetotal">{#str_LabelOrderShippingTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$shippingtotal}</span>
										</span>
									</div>

						{/if}

					{/if}

				{/if}

			{/if}
</div>

		<div class="final-order-summary">
				<span class="txt_linebreak"></span>
				<span class="txt_singleLineDivider"></span>

					{if ((($vouchersection=='TOTAL')&&($differenttaxrates==false)) || (($vouchersection=='TOTAL')&&($differenttaxrates)&&($specialvouchertype)))}

						{if (($vouchersection=='TOTAL')&&($differenttaxrates==false) && ($applyVoucherAsLineDiscount == false))}

								{* order before discount total row *}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading">{#str_LabelOrderSubTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$orderbeforediscounttotalvalue}</span>
										</span>
									</div>

								{* order total discount row *}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading">{$orderaftertotaldiscountname}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$ordertotaldiscountvalue}</span>
										</span>
									</div>
                        {else}
                            {if (($vouchersection=='TOTAL')&&($differenttaxrates==false) && ($applyVoucherAsLineDiscount == true))}
									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading">{#str_LabelOrderSubTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$ordersubtotal}</span>
										</span>
									</div>
						{/if}
						{/if}

					{else}

						{if ($differenttaxrates==false)&&($showpriceswithtax==false)&&(($hastotaltax==true)||($showzerotax==true))}

								{* order subtotal row *}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading">{#str_LabelOrderSubTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$ordersubtotal}</span>
										</span>
									</div>

						{/if}

					{/if}

							{if ($differenttaxrates == false) && ($showpriceswithtax == false) && (($hastotaltax == true) || ($showzerotax == true))}

								{* order tax row *}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading">{$itemtaxname} ({$itemtaxrate}%):</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$ordertotaltax}</span>
										</span>
									</div>
							{/if}


							{if ($ordergiftcardtotal > 0)}

								{* order total rows *}

									<div class="line-sub-total">
										<span class="total-heading">
											<span class="txt_total-heading">{#str_LabelOrderTotal#}:</span>
										</span>
										<span class="order-line-price">
											<span class="txt_price">{$ordertotal}</span>
										</span>
									</div>

								{* order tax row *}

								{if ($ordergiftcardtotal > 0 && $disabled_giftcard == '')}

							        {if (($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true))}

										<div class="line-sub-total">
											<span class="txt_includetaxtext">{$includestaxtotaltext}</span>
										</div>

									{/if}

										<div id="giftcard" class="line-sub-total {$disabled_giftcard}">
											<span class="total-heading">
												<span class="txt_total-heading">{#str_LabelGiftCard#}:</span>
											</span>
											<span class="order-line-price">
												<span class="txt_price">{$ordergiftcardtotalvalue}</span>
											</span>
										</div>
										<div id="giftcard-remain" class="line-sub-total {$disabled_giftcard}">
											<span class="total-heading">
												<span class="txt_total-heading">{#str_LabelGiftCardRemaining#}:</span>
											</span>
											<span class="order-line-price" id="giftcardbalance">
												<span class="txt_price">{$giftcardbalance}</span>
											</span>
										</div>

								{/if}

							{/if}

								{* order total row *}

								<div class="line-sub-total">
									<span class="total-heading">
										<span class="txt_total-heading txt_total-heading-em">{$labelamounttopay}:</span>
									</span>
									<span class="order-line-price" id="ordertotaltopayvalue">
										<span class="txt_price">{$ordertotaltopayvalue}</span>
									</span>
								</div>

							{if ($ordergiftcardtotal == 0 || $disabled_giftcard == 'disabled')}

								{if (($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true))}

									<div  class="line-sub-total" id="includetaxtextwithoutgiftcard">
										<span class="txt_includetaxtext">{$includestaxtotaltext}</span>
									</div>

								{/if}

							{/if}

		</div>	<!-- End order-summary -->
		<div class="headerbar"><div class="headertext"><span class="txt_sectionHeader">{#str_LabelShippingTitle#}</span></div></div>

					{* shipping *}

					<div class="shippingaddress_container">
						<span class="txt_shippingaddress">
							<span class="label_shippingaddress">{$shippingStoreAddressLabel}: </span> <span class="txt_linebreak">{$shippingaddress}</span>
						</span>
					</div>
					<div class="billingaddress_container">
						<span class="txt_billingaddress">
							<span class="label_billingaddress">{#str_LabelBillingAddress#}: </span> <span class="txt_linebreak">{$billingaddress}</span>
						</span>
					</div>		

					<span class="txt_linebreak"></span>

</div> <!-- content -->
