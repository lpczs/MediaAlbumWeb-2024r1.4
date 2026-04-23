{if $orderfootersections|@count > 0}
<div class="orderFooter" id="orderFooter">
	<div class="headerbar">
        <div class="headertext">{#str_LabelAdditionalItems#}</div>
    </div>
	{foreach from=$orderfootersections item=section} {* order footer sections *}
		{if $section.showcomponentname==true}
            <div style="padding: 10px;">
                <table width="600" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        {if $section.haspreview > 0 && $showthumbnail != 0}
                            <td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding:10px;">
                                <img class="componentPreview"  src="{$section.componentpreviewsrc|escape}" height="65" width="75">
                            </td>
                            <td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="200">
                                <span class="txt_componentname">
                                    {if $section.count <= 1 || $section.prompt == ''}
                                        <span class="section-title">{$section.sectionlabel}</span>
                                        <span> - </span>
                                        <span>{$section.itemcomponentname}</span>
                                    {else}
                                        <span class="section-title">{$section.sectionlabel}</span>
                                        <br>
                                        <span>{$section.itemcomponentname}</span>
                                    {/if}
                                </span>
                            </td>
                            <td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="130">
                                {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                                    <div class="paymentcomponentquantity">
                                        <span class="txt_qty">{$section.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding:10px;" width="130">
                                <span class="component-price">
                                    <span class="txt_price">{$section.totalsell}</span>
                                </span>
                            </td>
                        {else}
                            <td style="word-break:break-all; padding:10px;" width="295">
                                <span class="txt_componentname">
                                    {if $section.count <= 1 || $section.prompt == ''}
                                        <span class="section-title">{$section.sectionlabel}</span>
                                        <span> - </span>
                                        <span>{$section.itemcomponentname}</span>
                                    {else}
                                        <span class="section-title">{$section.sectionlabel}</span>
                                        <br>
                                        <span>{$section.itemcomponentname}</span>
                                    {/if}
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                                    <div class="paymentcomponentquantity" >
                                        <span class="txt_qty">{$section.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td align="right" style="word-break:break-all; padding:10px;" width="130">
                                <span class="component-price">
                                    <span class="txt_price">{$section.totalsell}</span>
                                </span>
                            </td>
                        {/if}

                    </tr>
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        <td width="535" style="word-break:break-all;  padding: 10px;" colspan="3">
                            {if $section.itemcomponentinfo != ''}
                                <div class="section-info">
                                    <span class="txt_itemcomponentinfo">
                                        {$section.itemcomponentinfo}
                                    </span>
                                </div>
                            {/if}
                            {if $section.itemcomponentpriceinfo != ''}
                                <div class="section-info">
                                    <span class="txt_itemcomponentpriceinfo">
                                        {$section.itemcomponentpriceinfo}
                                    </span>
                                </div>
                            {/if}
                        </td>
                    </tr>
                </table>

            </div>

            <div>
                {if $section.metadatahtml}
                    <span class="component-metadata">{$section.metadatahtml}</span>
                {/if}
            </div>


			<div class="line-total">
				{* VALUE OFF TOTAL VOUCHER *}
                {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
					{if ($section.discountvalueraw > 0)}
                    <div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$section.totalsell}</span></span>
                    </div>
                    <div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.discountname}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$section.discountvalue}</span></span>
                    </div>
                    {/if}
                    {if (!$showpriceswithtax)}
                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                        {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$section.subtotal}</span></span>
                            </div>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.taxratename} ({$section.taxrate}%):</span></span>
                                <span class="order-line-price"><span class="txt_price">{$section.displaytax}</span></span>
                            </div>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$section.displayprice}</span></span>
                            </div>
                        {else}
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$section.displayprice}</span></span>
                            </div>
                        {/if}
                    {else}
                        <div class="comp-sub-total">
                            <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$section.subtotal}</span>
                        </div>

                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
						{if ($showtaxbreakdown)}
							{if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
								<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext">{$section.includesitemtaxtext}</span></div>
							{/if}
                        {/if}
                    {/if}
                {else}

                    {* DIFFERNETTAXRATES AND SHOWPRICES WITH TAX *}
                    {if (($differenttaxrates) && ($showpriceswithtax)) }

						{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$section.totalsell}</span></span>
								</div>
							{if ($section.discountvalueraw > 0)}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.discountname}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$section.discountvalue}</span></span>
								</div>

								{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
	                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$section.subtotal}</span></span>
								</div>
								{/if}
							{/if}
							{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
							{if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.taxratename} ({$section.taxrate}%):</span></span>
									<span class="order-line-price"><span class="txt_price">{$section.displaytax}</span></span>
								</div>
							{/if}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$section.displayprice}</span></span>
								</div>
						{else}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$section.subtotal}</span></span>
								</div>

							{* SHOWTAXBREAKDOWN *}
							{if ($showtaxbreakdown)}
								{if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
								<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext">{$section.includesitemtaxtext}</span></div>
								{/if}
							{/if}
	                    {/if}

					{/if}

                    {* DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX *}
                    {if (($differenttaxrates) && (!$showpriceswithtax)) }

                        {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.totalsell}</span></span>
									</div>
                            {if ($section.discountvalueraw > 0)}
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.discountname}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.discountvalue}</span></span>
									</div>
									{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
									{if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.subtotal}</span></span>
									</div>
									{/if}
                            {/if}
                            {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.taxratename} ({$section.taxrate}%):</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.displaytax}</span></span>
									</div>
                            {/if}
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.displayprice}</span></span>
									</div>
                        {else}

							{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
							{if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}

									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.totalsell}</span></span>
									</div>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.taxratename} ({$section.taxrate}%):</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.displaytax}</span></span>
									</div>
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.displayprice}</span></span>
									</div>
							{else}
									<div class="comp-sub-total">
										<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$section.displayprice}</span></span>
									</div>
							{/if}

                    	{/if}

                    {/if}

						{* NOT DIFFERNETTAXRATES *}
						{if (!$differenttaxrates)}
                            {if (($section.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
							<div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$section.totalsell}</span></span>
                            </div>
                            <div class="comp-sub-total">
                                <span class="total-heading"><span class="txt_orderfooterlinetotal">{$section.discountname}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$section.discountvalue}</span></span>
                            </div>
                            {/if}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$section.subtotal}</span></span>
							</div>
						{/if}
                {/if}
			</div>
		{/if}

		<!-- sub-sections of order footer component start -->
		<div class="clear"></div>

		{foreach from=$section.subsections item=subsection} {* subsections of a section *}
		{if $subsection.showcomponentname==true}
		<div style="padding: 10px;">
		<table width="600" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
			<tr style="color: #575757; font-family: Lucida Grande;">
				{if $subsection.haspreview > 0 && $showthumbnail != 0}
					<td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding:10px;">
						<img class="componentPreview"  src="{$subsection.componentpreviewsrc|escape}" height="65" width="75">
					</td>
					<td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="200">
						<span class="txt_subcomponentname">
							{if $subsection.count <= 1 || $subsection.prompt == ''}
								<span class="section-title">{$subsection.sectionlabel}</span><span> - </span>
								<span>{$subsection.itemcomponentname}</span>
							{else}
								<span class="section-title">{$subsection.sectionlabel}</span>
								<br>
								<span>{$subsection.itemcomponentname}</span>
							{/if}
						</span>
					</td>
					<td bgcolor="#FFFFFF" style="word-break:break-all; padding:10px;" width="130">
						{if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
							<div class="paymentsubcomponentquantity">
								<span class="txt_subqty">{$subsection.quantity}</span>
							</div>
						{/if}
					</td>
					<td bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding:10px;" width="130">
						<span class="component-price"><span class="txt_price">{$subsection.totalsell}</span></span>
					</td>
				{else}
					<td style="word-break:break-all; padding:10px;" width="295">
						<span class="txt_subcomponentname">
							{if $subsection.count <= 1 || $subsection.prompt == ''}
								<span class="section-title">{$subsection.sectionlabel}</span><span> - </span>
								<span>{$subsection.itemcomponentname}</span>
							{else}
								<span class="section-title">{$subsection.sectionlabel}</span>
								<br>
								<span>{$subsection.itemcomponentname}</span>
							{/if}
						</span>
					</td>
					<td style="word-break:break-all; padding: 10px;" width="130">
						{if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
							<div class="paymentsubcomponentquantity">
								<span class="txt_subqty">{$subsection.quantity}</span>
							</div>
						{/if}
					</td>
					<td align="right" style="word-break:break-all; padding:10px;" width="130">
						<span class="component-price"><span class="txt_price">{$subsection.totalsell}</span></span>
					</td>
				{/if}

			</tr>
			<tr style="color: #575757; font-family: Lucida Grande;">

				<td width="535" style="word-break:break-all;  padding: 10px;" colspan="3">

					{if ($subsection.itemcomponentinfo != '' || $subsection.itemcomponentpriceinfo != '')}
						<div style="background-color:white; width:100%; min-height:32px; word-wrap: break-word">
							{if $subsection.itemcomponentinfo != ''}
								<div class="section-info">
									<span class="txt_itemsubcomponentinfo">
										{$subsection.itemcomponentinfo}
									</span>
								</div>
							{/if}
							{if $subsection.itemcomponentpriceinfo != ''}
								<div class="section-info">
									<span class="txt_itemsubcomponentpriceinfo">
										{$subsection.itemcomponentpriceinfo}
									</span>
								</div>
							{/if}
						</div>
					{/if}
				</td>
			</tr>

			<tr style="color: #575757; font-family: Lucida Grande;">
				<td width="535" style="word-break:break-all;  padding: 10px;" colspan="3">
					{if $subsection.metadatahtml}
                        <span id="metadatarow_{$subsection.orderlineid}" class="component-metadata">
                            <span class="txt_subComponentMetaData">
                                {$subsection.metadatahtml}
                            </span>
                        </span>
                    {/if}
				</td>
			</tr>
		</table>
    </div>

			<div class="clear"></div>
			<div class="line-total">
				{* VALUE OFF TOTAL VOUCHER *}
				{if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
					{if ($subsection.discountvalueraw > 0)}
					<div class="comp-sub-total">
						<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
						<span class="order-line-price"><span class="txt_price">{$subsection.totalsell}</span></span>
					</div>
					<div class="comp-sub-total">
						<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.discountname}:</span></span>
						<span class="order-line-price"><span class="txt_price">{$subsection.discountvalue}</span></span>
					</div>
					{/if}
					{if (!$showpriceswithtax)}
						{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
						{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$subsection.subtotal}</span></span>
							</div>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.taxratename} ({$subsection.taxrate}%):</span></span>
								<span class="order-line-price"><span class="txt_price">{$subsection.displaytax}</span></span>
							</div>
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$subsection.displayprice}</span></span>
							</div>
						{else}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$subsection.displayprice}</span></span>
							</div>
						{/if}
					{else}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$subsection.subtotal}</span></span>
						</div>

						{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
						{if ($showtaxbreakdown)}
							{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
								<div class="comp-sub-total"><span class="txt_sub_orderfooterincludetaxtext">{$subsection.includesitemtaxtext}</span></div>
							{/if}
						{/if}
					{/if}
				{else}

					{* DIFFERNETTAXRATES AND SHOWPRICES WITH TAX *}
					{if (($differenttaxrates) && ($showpriceswithtax)) }

						{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$subsection.totalsell}</span></span>
							</div>
							{if ($subsection.discountvalueraw > 0)}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.discountname}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.discountvalue}</span></span>
								</div>
								{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
								{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.subtotal}</span></span>
								</div>
								{/if}
							{/if}
							{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
							{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.taxratename} ({$subsection.taxrate}%):</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displaytax}</span></span>
								</div>
							{/if}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displayprice}</span></span>
								</div>
						{else}

							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$subsection.subtotal}</span></span>
							</div>

							{* SHOWTAXBREAKDOWN *}
							{if ($showtaxbreakdown)}
								{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
								<div class="comp-sub-total"><span class="txt_sub_orderfooterincludetaxtext">{$subsection.includesitemtaxtext}</span></div>
								{/if}
							{/if}
						{/if}

					{/if}

					{* DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX *}
					{if (($differenttaxrates) && (!$showpriceswithtax)) }

						{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.totalsell}</span></span>
								</div>
							{if ($subsection.discountvalueraw > 0)}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.discountname}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.discountvalue}</span></span>
								</div>
								{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
								{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.subtotal}</span></span>
								</div>
								{/if}
							{/if}
							{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
							{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.taxratename} ({$subsection.taxrate}%):</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displaytax}</span></span>
								</div>
							{/if}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displayprice}</span></span>
								</div>
						{else}

							{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
							{if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}

								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.totalsell}</span></span>
								</div>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$subsection.taxratename} ({$subsection.taxrate}%):</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displaytax}</span></span>
								</div>
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displayprice}</span></span>
								</div>
							{else}
								<div class="comp-sub-total">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$subsection.displayprice}</span></span>
								</div>
							{/if}

						{/if}

					{/if}

					{* NOT DIFFERNETTAXRATES *}
					{if (!$differenttaxrates)}
                        {if (($subsection.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
						<div class="comp-sub-total">
                            <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$subsection.totalsell}</span></span>
                        </div>
                        <div class="comp-sub-total">
                            <span class="total-heading"><span class="txt_orderfooterlinetotal">{$subsection.discountname}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$subsection.discountvalue}</span></span>
                        </div>
                        {/if}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$subsection.subtotal}</span></span>
						</div>

					{/if}
				{/if}
			</div>
		{/if}

		<div class="clear"></div>

		{/foreach} {* subsections of a section *}
		<!-- sub-sections of order footer component end -->

		<!-- checkboxes inside component start -->

		{foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}
		{if $checkbox.showcomponentname==true}
			{if $checkbox.checked == 1}
                <table width="600" class="orderfootersubsection" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                    <tr>
                    {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                        <td width="75" align="left" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview" src="{$checkbox.componentpreviewsrc}" height="65" width="75" />
                        </td>
                        <td width="200" align="left" style="word-break:break-all; padding: 10px;">
                            <span class="txt_subcomponentname">
                                <span class="component-name">{$checkbox.itemcomponentprompt}</span><span>  - {$checkbox.itemcomponentname}</span>
                            </span>
                        </td>
                        <td width="130" align="left" class="txt_subcomponentqty" style="word-break:break-all; padding: 10px;">
                            {if $checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8 }
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty">{$checkbox.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td width="130" align="right" style="word-break:break-all; padding: 10px;">
                            <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                        </td>
                    {else}
                        <td width="295" align="left" style="word-break:break-all; padding: 10px;">

                            <span class="txt_subcomponentname">
                                <span class="component-name">{$checkbox.itemcomponentprompt}</span><span>  - {$checkbox.itemcomponentname}</span>
                            </span>
                        </td>
                        <td width="130" align="left" class="txt_subcomponentqty" style="word-break:break-all; padding: 10px;">
                            {if $checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8 }
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty">{$checkbox.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td width="130" align="right" style="word-break:break-all; padding: 10px;">
                            <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                        </td>
                       {/if}
                    </tr>

                    {if ($checkbox.itemcomponentinfo != '' || $checkbox.itemcomponentpriceinfo != '')}
                    <tr>
                        {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                        <td colspan="3" width="460" style="word-break:break-all; padding: 10px;">
                        {else}
                        <td colspan="3" width="535" style="word-break:break-all; padding: 10px;">
                        {/if}
                            {if $checkbox.itemcomponentinfo != ''}<div class="checkbox-info"><span class="txt_itemsubcomponentinfo">{$checkbox.itemcomponentinfo}</span></div>{/if}
                            {if $checkbox.itemcomponentpriceinfo != ''}<div class="checkbox-info"><span class="txt_itemsubcomponentpriceinfo">{$checkbox.itemcomponentpriceinfo}</span></div>{/if}
                        </td>
                    </tr>
                    {/if}

                </table>

                {if $checkbox.metadatahtml}
                    <span id="metadatarow_{$checkbox.orderlineid}" class="component-metadata oderfootersubmetadata{if not $checkbox.checked} invisible{/if}"><span class="txt_subComponentMetaData">{$checkbox.metadatahtml}</span></span>
                {/if}

				<div class="line-total">
					{* VALUE OFF TOTAL VOUCHER *}
						{if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
							{if ($checkbox.discountvalueraw > 0)}
							<div class="comp-sub-total" style="padding: 10px;">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
							</div>
							<div class="comp-sub-total" style="padding: 10px;">
								<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
							</div>
							{/if}
							{if (!$showpriceswithtax)}
								{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
								{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
									</div>
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
										<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
									</div>
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
									</div>
								{else}
									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
									</div>
								{/if}
							{else}
								<div class="comp-sub-total" style="padding: 10px;">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
								</div>

								{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
								{if ($showtaxbreakdown)}
									{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
										<div class="comp-sub-total" style="padding: 10px;"><span class="txt_sub_orderfooterincludetaxtext">{$checkbox.includesitemtaxtext}</span></div>
									{/if}
								{/if}
							{/if}
						{else}

							{* DIFFERNETTAXRATES AND SHOWPRICES WITH TAX *}
							{if (($differenttaxrates) && ($showpriceswithtax)) }


								{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
										</div>
									{if ($checkbox.discountvalueraw > 0)}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
										</div>
										{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
										{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
										</div>
										{/if}
									{/if}
									{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
									{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
										</div>
									{/if}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
										</div>
								{else}

									<div class="comp-sub-total" style="padding: 10px;">
										<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
										<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
									</div>

									{* SHOWTAXBREAKDOWN *}
									{if ($showtaxbreakdown)}
										{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
										<div class="comp-sub-total" style="padding: 10px;"><span class="txt_sub_orderfooterincludetaxtext">{$checkbox.includesitemtaxtext}</span></div>
										{/if}
									{/if}

								{/if}

							{/if}

							{* DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX *}
							{if (($differenttaxrates) && (!$showpriceswithtax)) }

								{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
										</div>
									{if ($checkbox.discountvalueraw > 0)}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
										</div>
										{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
										{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
										</div>
										{/if}
									{/if}
									{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
									{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
										</div>
									{/if}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
										</div>
								{else}

									{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
									{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}

										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
										</div>
										<div class="comp-sub-total" style="padding: 10px; background-color: white;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
										</div>
										<div class="comp-sub-total" style="padding: 10px; background-color: white;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
										</div>

									{else}
										<div class="comp-sub-total" style="padding: 10px;">
											<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
											<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
										</div>
									{/if}

								{/if}
							{/if}

							{* NOT DIFFERNETTAXRATES *}
							{if (!$differenttaxrates)}
                                {if (($checkbox.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
								<div class="comp-sub-total" style="padding: 10px;">
                                    <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
                                    <span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                                </div>
                                <div class="comp-sub-total" style="padding: 10px;">
                                    <span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
                                    <span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
                                </div>
                                {/if}
								<div class="comp-sub-total" style="padding: 10px;">
									<span class="total-heading"><span class="txt_subcomp_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
									<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span>
								</div>
							{/if}
						{/if}
				</div>
			{/if}
		{/if}

	{/foreach} {* checkboxes of a section *}
		<!-- checkboxes inside component end -->
</div>
<div class="clear"></div>

{/foreach} {* order footer sections *}

<!-- orderfooter checkboxes start -->
{foreach from=$orderfootercheckboxes item=checkbox}
{if $checkbox.showcomponentname==true}
	{if $checkbox.checked == 1}
        <table width="600" class="orderfootersection" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
            <tr>
                {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                <td width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                    <img class="componentPreview" src="{$checkbox.componentpreviewsrc}" height="65" width="75" />
                </td>
                <td width="200" align="left" style="word-break:break-all; padding: 10px;">
                    <span>
                        <span class="section-title">{$checkbox.itemcomponentprompt}</span> <span> - {$checkbox.itemcomponentname}</span>
                    </span>
                </td>
                <td width="130" align="left" class="txt_componentqty" style="word-break:break-all; padding: 10px;">
                    {if $checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8 }
                        <div class="paymentsubcomponentquantity">
                            <span class="txt_qty">{$checkbox.quantity}</span>
                        </div>
                    {/if}
                </td>
                <td width="130" align="right" style="word-break:break-all; padding: 10px;">
                    <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                </td>
                {else}
                <td width="275" align="left" bgcolor="white" style="word-break:break-all; padding: 10px;">

                    <span>
                        <span class="section-title">{$checkbox.itemcomponentprompt}</span> <span> - {$checkbox.itemcomponentname}</span>
                    </span>
                </td>
                <td width="130" align="left" class="txt_componentqty" style="word-break:break-all; padding: 10px;">
                    {if $checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8 }
                        <div class="paymentsubcomponentquantity">
                            <span class="txt_qty">{$checkbox.quantity}</span>
                        </div>
                    {/if}
                </td>
                <td width="150" align="right" style="word-break:break-all; padding: 10px;">
                    <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                </td>
                {/if}
            </tr>

            {if ($checkbox.itemcomponentinfo != '' || $checkbox.itemcomponentpriceinfo != '')}
            <tr>
                {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                <td colspan="3" width="460" style="word-break:break-all; padding: 10px;">
                {else}
                <td colspan="3" width="535" style="word-break:break-all; padding: 10px;">
                {/if}
                    {if $checkbox.itemcomponentinfo != ''}<div class="checkbox-info"><span class="txt_itemcomponentinfo">{$checkbox.itemcomponentinfo}</span></div>{/if}
                    {if $checkbox.itemcomponentpriceinfo != ''}<div class="checkbox-info"><span class="txt_itemcomponentpriceinfo">{$checkbox.itemcomponentpriceinfo}</span></div>{/if}
                </td>
            </tr>
            {/if}
        </table>

        {if ($checkbox.metadatahtml) }
            <span id="metadatarow_{$checkbox.orderlineid}" class="component-metadata oderfootermetadata {if not $checkbox.checked} invisible{/if}">{$checkbox.metadatahtml}</span>
        {/if}

		<div class="line-total">
			{* VALUE OFF TOTAL VOUCHER *}
			{if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
				{if ($checkbox.discountvalueraw > 0)}
				<div class="comp-sub-total">
					<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
					<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
				</div>
				<div class="comp-sub-total">
					<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
					<span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
				</div>
				{/if}
				{if (!$showpriceswithtax)}
					{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
					{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
						</div>
					{else}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
						</div>
					{/if}
				{else}
					<div class="comp-sub-total">
						<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
						<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
					</div>

					{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
					{if ($showtaxbreakdown)}
						{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
							<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext">{$checkbox.includesitemtaxtext}</span></div>
						{/if}
					{/if}
				{/if}
			{else}

				{* DIFFERNETTAXRATES AND SHOWPRICES WITH TAX *}
				{if (($differenttaxrates) && ($showpriceswithtax)) }

					{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
							</div>
						{if ($checkbox.discountvalueraw > 0)}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
							</div>
                            {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
							</div>
							{/if}
						{/if}

						{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
						{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
							</div>
						{/if}
							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
							</div>
					{else}

							<div class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
							</div>

						{* SHOWTAXBREAKDOWN *}
						{if ($showtaxbreakdown)}
							{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
								<div class="comp-sub-total"><span class="txt_orderfooterincludetaxtext">{$checkbox.includesitemtaxtext}</span></div>
							{/if}
						{/if}
					{/if}

				{/if}

				{* DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX *}
				{if (($differenttaxrates) && (!$showpriceswithtax)) }

				{if (($vouchersection=='TOTAL') && ($specialvouchertype))}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
						</div>
					{if ($checkbox.discountvalueraw > 0)}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
						</div>
						{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
						{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
						</div>
						{/if}
					{/if}
					{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
					{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
						</div>
					{/if}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
						</div>
				{else}

					{* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
					{if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw > 0) ) )}

						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displaytax}</span></span>
						</div>
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
						</div>
					{else}
						<div class="comp-sub-total">
							<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
							<span class="order-line-price"><span class="txt_price">{$checkbox.displayprice}</span></span>
						</div>
					{/if}
				{/if}
			{/if}
				{* NOT DIFFERNETTAXRATES *}
				{if (!$differenttaxrates)}
                    {if (($checkbox.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
					<div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelSubTotal#}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                    </div>
                    <div class="comp-sub-total">
                        <span class="total-heading"><span class="txt_orderfooterlinetotal">{$checkbox.discountname}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$checkbox.discountvalue}</span></span>
                    </div>
                    {/if}
                    <div class="comp-sub-total">
						<span class="total-heading"><span class="txt_orderfooterlinetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
						<span class="order-line-price"><span class="txt_price">{$checkbox.subtotal}</span></span>
					</div>
				{/if}
			{/if}
		</div>
	{/if}
{/if}

{/foreach}
<!-- order footer checkboxes end -->

<span class="txt_linebreak"></span>
	<div class="orderfooter-sub-total">
		{if ($showpriceswithtax == false)}
			{if (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0)))}
				{if ($differenttaxrates)}
					<span class="comp-sub-total">
						<span class="total-heading"><span class="txt_orderfooterfinaltotal">{$orderfootersubtotalname}:</span></span>
						<span class="order-line-price"><span class="txt_price">{$orderfootersubtotal}</span></span>
					</span>
				{/if}
				{if ($showtaxbreakdown)}
					{if ($differenttaxrates)}
						{if ($footertaxratesequal == 1)}
							<span class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterfinaltotal">{$orderfootertaxname} ({$orderfootertaxrate}%):</span></span>
								<span class="order-line-price"><span class="txt_price">{$orderfootertaxtotal}</span></span>
							</span>
						{else}
							<span class="comp-sub-total">
								<span class="total-heading"><span class="txt_orderfooterfinaltotal">{$orderfootertaxname}:</span></span>
								<span class="order-line-price"><span class="txt_price">{$orderfootertaxtotal}</span></span>
							</span>
						{/if}
					{/if}
				{/if}
			{/if}
		{/if}

		{if ($showpriceswithtax == false) && (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0))) && ($differenttaxrates)}
			<span class="comp-sub-total">
				<span class="total-heading"><span class="txt_orderfooterfinaltotal">{$orderfootertotalname}:</span></span>
				<span class="order-line-price"><span class="txt_price">{$orderfootertotal}</span></span>
			</span>
		{else}
			<span class="comp-sub-total">
				<span class="total-heading"><span class="txt_orderfooterfinaltotal">{$orderfootersubtotalname}:</span></span>
				<span class="order-line-price"><span class="txt_price">{$orderfootersubtotal}</span></span>
			</span>
		{/if}

		{if ($showpriceswithtax)}
			{if (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0)))}
				{if ($showtaxbreakdown)}
					{if ($differenttaxrates)}
						<div class="comp-sub-total">
							<span class="txt_includetaxtextorderfooterfinal">{$includesorderfootertaxtext}</span>
						</div>
					{/if}
				{/if}
			{/if}
		{/if}

	</div>
</div>
{/if}

<div class="clear"></div>