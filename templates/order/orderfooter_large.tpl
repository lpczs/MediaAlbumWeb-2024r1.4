{if ($orderfootersections|@sizeof > 0) || ($orderfootercheckboxes|@sizeof > 0)}
    {assign var="multilinedesc" value="false"}
    {foreach from=$orderfootersections item=section} {* order footer sections *}
        {if $section.showcomponentname==true}
            {if !isset($bTitleOrder)}
    {if $call_action == 'init'}
<div class="orderFooter" id="orderFooter">
    {/if}
    <div class="sectionLabelLegendFooter">
        {#str_LabelAdditionalItems#}
		<span class="linkToggleFooter">
            {if $stage == 'qty'}
            <span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white">{#str_OrderHide#}</span>
            {else}
            <span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white">{#str_OrderShow#}</span>
            {/if}
		</span>
    </div>
    <div id="contentFooter" class="content" {if $stage=='payment'}style="display:none;"{/if}>
                {assign var="bTitleOrder" value="true"}
            {/if}
        <div id="componentrow_{$section.orderlineid}" class="componentbloc">
			<div class="section-title-header">
			{if ($section.sectionlabel != '')}
				<span class="section-category-name">{$section.sectionlabel}:</span> <span class="section-category-prompt">{$section.prompt}</span>
			{/if}
			</div>

            <div class="componentrow">
            {if $section.haspreview > 0}
                <img class="component-preview" src="{$section.componentpreviewsrc|escape}" alt=""/>
                <div class="componentSectionTitle">
            {else}
                <div class="componentSectionTitleLong">
            {/if}
                    <div class="componentContentText">
                        <div class="section-title">{$section.itemcomponentname}</div>
            {if $section.haspreview > 0}
                {if !empty($section.itemcomponentinfo)}
                        <div class="section-info">
                            {$section.itemcomponentinfo}
                        </div>
                    {assign var="multilinedesc" value="true"}
                {/if}
                {if ! empty($section.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo">
                            <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                        </div>
                    {assign var="multilinedesc" value="true"}
                {/if}
                {if !empty($section.itemcomponentpriceinfo)}
                        <div class="section-info">
                            {$section.itemcomponentpriceinfo}
                        </div>
                    {assign var="multilinedesc" value="true"}
                {/if}
            {else}
                {if !empty($section.itemcomponentinfo)}
                        <div class="section-info-long">
                            {$section.itemcomponentinfo}
                        </div>
                    {assign var="multilinedesc" value="true"}
                {/if}
                {if ! empty($section.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo-long">
                            <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                        </div>
                    {assign var="multilinedesc" value="true"}
                {/if}
                {if !empty($section.itemcomponentpriceinfo)}
                        <div class="section-info-long">
                            {$section.itemcomponentpriceinfo}
                        </div>
                    {assign var="multilinedesc" value="true"}
                {/if}
            {/if}
                        <div class="clear"></div>
                    </div>
            <!-- START add-edit-change-remove links -->
            {if $stage=='qty'}
                {foreach from=$section.itemcomponentbuttons item=button}
                    <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="-1" data-sectionlineid="{$section.orderlineid}">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle {$button.class}">{$button.label}</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                {/foreach}
            {/if}
            <!-- END add-edit-change-remove links -->
                </div>
            {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                {if ($stage=='payment')}
                    <span class="quantityText">{$section.quantity}</span>
                {else}
                    <input id="hiddeqty_{$section.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$section.quantity}"/>
                    {if empty($section.itemqtydropdown)}
                    <input id="itemqty_{$section.orderlineid}" type="text" class="quantity" maxlength="8" value="{$section.quantity}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="keyup" />
                    <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="click" />
                    {else}
                    <select id="itemqty_{$section.orderlineid}" class="" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="change">
                        {foreach from=$section.itemqtydropdown item=qtyValue}
                        <option {if $qtyValue==$section.quantity}selected="selected"{/if} value={$qtyValue}>{$qtyValue}</option>
                        {/foreach}
                    </select>
                    {/if}
                {/if}
                </div>
            {/if}
                <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                    {$section.totalsell}
                </div>
                {if $multilinedesc}
                    {assign var="multilinedesc" value="false"}
                {/if}
                <div class="clear"></div>
            </div>
            {if $section.metadatahtml}
            <div id="metadatarow_{$section.orderlineid}" class="component-metadata">
                {$section.metadatahtml}
            </div>
            {/if}
            {if $stage=='payment'}
                <!-- VALUE OFF TOTAL VOUCHER -->
            <div class="line-total">
                {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
                    {if ($section.discountvalueraw > 0)}
                <div class="line-sub-total">
                    <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                    <span class="discount-price">{$section.totalsell}</span>
                </div>
                <div class="line-sub-total">
                    <span class="discount-heading">{$section.discountname}:</span>
                    <span class="discount-price">{$section.discountvalue}</span>
                </div>
                    {/if}
                    {if (!$showpriceswithtax)}
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                        {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                <div class="line-sub-total">
                    <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                    <span class="discount-price">{$section.subtotal}</span>
                </div>
                <div class="line-sub-total">
                    <span class="discount-heading">{$section.taxratename} ({$section.taxrate}%):</span>
                    <span class="discount-price">{$section.displaytax}</span>
                </div>
                <div class="line-sub-total">
                    <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                    <span class="discount-price">{$section.displayprice}</span>
                </div>
                        {else}
                <div class="line-sub-total">
                    <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                    <span class="discount-price">{$section.displayprice}</span>
                </div>
                        {/if}
                    {else}
                <div class="line-sub-total">
                    <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                    <span class="discount-price">{$section.subtotal}</span>
                </div>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                        {if ($showtaxbreakdown)}
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                <div class="line-sub-total-small-bottom">{$section.includesitemtaxtext}</div>
                            {/if}
                        {/if}
                    {/if}
                {else}
                    <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                    {if (($differenttaxrates) && ($showpriceswithtax)) }
                        {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$section.totalsell}</span>
                    </div>
                            {if ($section.discountvalueraw > 0)}
                    <div class="line-sub-total">
                        <span class="discount-heading">{$section.discountname}:</span>
                        <span class="discount-price">{$section.discountvalue}</span>
                    </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$section.subtotal}</span>
                    </div>
                            {/if}
                            {/if}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                    <div class="line-sub-total">
                        <span class="discount-heading">{$section.taxratename} ({$section.taxrate}%):</span>
                        <span class="discount-price">{$section.displaytax}</span>
                    </div>
                            {/if}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                        <span class="discount-price">{$section.displayprice}</span>
                    </div>
                        {else}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                        <span class="discount-price">{$section.subtotal}</span>
                    </div>
                            <!-- SHOWTAXBREAKDOWN -->
                            {if ($showtaxbreakdown)}
                                {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                    <div class="line-sub-total-small-bottom">{$section.includesitemtaxtext}</div>
                                {/if}
                            {/if}
                        {/if}
                    {/if}
                    <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                    {if (($differenttaxrates) && (!$showpriceswithtax)) }
                        {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$section.totalsell}</span>
                    </div>
                            {if ($section.discountvalueraw > 0)}
                    <div class="line-sub-total">
                        <span class="discount-heading">{$section.discountname}:</span>
                        <span class="discount-price">{$section.discountvalue}</span>
                    </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$section.subtotal}</span>
                    </div>
                            {/if}
                            {/if}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                    <div class="line-sub-total">
                        <span class="discount-heading">{$section.taxratename} ({$section.taxrate}%):</span>
                        <span class="discount-price">{$section.displaytax}</span>
                    </div>
                            {/if}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                        <span class="discount-price">{$section.displayprice}</span>
                    </div>
                        {else}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($section.displaytaxraw>0) ) )}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$section.totalsell}</span>
                    </div>
                    <div class="line-sub-total">
                        <span class="discount-heading">{$section.taxratename} ({$section.taxrate}%):</span>
                        <span class="discount-price">{$section.displaytax}</span>
                    </div>
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                        <span class="discount-price">{$section.displayprice}</span>
                    </div>
                            {else}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                        <span class="discount-price">{$section.displayprice}</span>
                    </div>
                            {/if}
                        {/if}

                    {/if}
                    <!-- NOT DIFFERNETTAXRATES -->
                    {if (!$differenttaxrates)}
                            {if (($section.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$section.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$section.discountname}:</span>
                <span class="discount-price">{$section.discountvalue}</span>
            </div>
                            {/if}
                            <div class="line-sub-total">
                    <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                    <span class="discount-price">{$section.subtotal}</span>
                </div>
                    {/if}
                {/if}
            </div>
            {/if}

            <!-- sub-sections of order footer component start -->
        {foreach from=$section.subsections item=subsection} {* subsections of a section *}
            {if $subsection.showcomponentname==true}
        <div id="componentrow_{$subsection.orderlineid}" class="subsection">
            <div class="subsectionBloc">
				<div class="section-title-header">
				{if ($subsection.sectionlabel != '')}
					<span class="section-category-name">{$subsection.sectionlabel}:</span> <span class="section-category-prompt">{$subsection.prompt}</span>
				{/if}
				</div>
                {if $subsection.haspreview > 0}
                <img class="component-preview" src="{$subsection.componentpreviewsrc|escape}" alt="" />
                <div class="componentSubSectionTitle">
                {else}
                <div class="componentSubSectionTitleLong">
                {/if}
                    <div class="componentContentText">
                        <div class="section-title">{$subsection.itemcomponentname}</div>
                {if $subsection.haspreview > 0}
                    {if !empty($subsection.itemcomponentinfo)}
                        <div class="subsection-info">
                            {$subsection.itemcomponentinfo}
                        </div>
                        {assign var="multilinedesc" value="true"}
                    {/if}
                    {if ! empty($subsection.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo">
                            <a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
                        </div>
                        {assign var="multilinedesc" value="true"}
                    {/if}
                    {if !empty($subsection.itemcomponentpriceinfo)}
                        <div class="subsection-info">
                            {$subsection.itemcomponentpriceinfo}
                        </div>
                        {assign var="multilinedesc" value="true"}
                    {/if}
                {else}
                    {if !empty($subsection.itemcomponentinfo)}
                        <div class="subsection-info-long">
                            {$subsection.itemcomponentinfo}
                        </div>
                        {assign var="multilinedesc" value="true"}
                    {/if}
                    {if ! empty($subsection.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo-long">
                            <a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
                        </div>
                        {assign var="multilinedesc" value="true"}
                    {/if}
                    {if !empty($subsection.itemcomponentpriceinfo)}
                        <div class="subsection-info-long">
                            {$subsection.itemcomponentpriceinfo}
                        </div>
                        {assign var="multilinedesc" value="true"}
                    {/if}
                {/if}
                        <div class="clear"></div>
                    </div>
                <!-- START add-edit-change-remove links -->
                {if $stage=='qty'}
                    {foreach from=$subsection.itemcomponentbuttons item=button}
                    <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="-1" data-sectionlineid="{$subsection.orderlineid}">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle {$button.class}">{$button.label}</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                    {/foreach}
                {/if}
                <!-- END add-edit-change-remove links -->

                </div>
                {if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
                <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                    {if ($stage=='payment')}
                    <span class="quantityText">
                        {$subsection.quantity}
                    </span>
                    {else}
                    <input id="hiddeqty_{$subsection.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$subsection.quantity}"/>
                        {if empty($subsection.itemqtydropdown)}
                    <input id="itemqty_{$subsection.orderlineid}" type="text" class="quantity" maxlength="8" value="{$subsection.quantity}" data-decorator="fnUpdateComponentQty" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" data-trigger="keypress" />
                    <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" data-trigger="click">
                        {else}
                    <select id="itemqty_{$subsection.orderlineid}" class="" data-decorator="fnUpdateComponentQty" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" data-trigger="change" >
                            {foreach from=$subsection.itemqtydropdown item=qtyValue}
                        <option {if $qtyValue==$subsection.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                            {/foreach}
                    </select>
                        {/if}
                    {/if}
                </div>
                {/if}
                <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                    {$subsection.totalsell}
                </div>
                {if $multilinedesc}
                    {assign var="multilinedesc" value="false"}
                {/if}
                <div class="clear"></div>
            </div>
                {if $subsection.metadatahtml}
            <div id="metadatarow_{$subsection.orderlineid}" class="subcomponent-metadata">
                {$subsection.metadatahtml}
            </div>
                {/if}
        </div>
        <div class="clear"></div>
                {if $stage=='payment'}
        <div class="line-total">
                    <!-- VALUE OFF TOTAL VOUCHER -->
                    {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
                        {if ($subsection.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.discountname}:</span>
                <span class="discount-price">{$subsection.discountvalue}</span>
            </div>
                        {/if}
                        {if (!$showpriceswithtax)}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.subtotal}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.taxratename} ({$subsection.taxrate}%):</span>
                <span class="discount-price">{$subsection.displaytax}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.displayprice}</span>
            </div>
                            {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.displayprice}</span>
            </div>
                            {/if}
                        {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.subtotal}</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if ($showtaxbreakdown)}
                                {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total-small-bottom">{$subsection.includesitemtaxtext}</div>
                                {/if}
                            {/if}
                        {/if}
                    {else}
                        <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                        {if (($differenttaxrates) && ($showpriceswithtax)) }
                        {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.totalsell}</span>
            </div>
                            {if ($subsection.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.discountname}:</span>
                <span class="discount-price">{$subsection.discountvalue}</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.subtotal}</span>
            </div>
                            {/if}
                            {/if}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.taxratename} ({$subsection.taxrate}%):</span>
                <span class="discount-price">{$subsection.displaytax}</span>
            </div>
                            {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.displayprice}</span>
            </div>
                        {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.subtotal}</span>
            </div>
                            <!-- SHOWTAXBREAKDOWN -->
                            {if ($showtaxbreakdown)}
                                {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total-small-bottom">{$subsection.includesitemtaxtext}</div>
                                {/if}
                            {/if}
                            {/if}
                        {/if}
                        <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                        {if (($differenttaxrates) && (!$showpriceswithtax)) }
                            {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.totalsell}</span>
            </div>
                                {if ($subsection.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.discountname}:</span>
                <span class="discount-price">{$subsection.discountvalue}</span>
            </div>
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.subtotal}</span>
            </div>
                                    {/if}
                                {/if}
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.taxratename} ({$subsection.taxrate}%):</span>
                <span class="discount-price">{$subsection.displaytax}</span>
            </div>
                                {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.displayprice}</span>
            </div>
                            {else}
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                {if (($showzerotax) || ( (!$showzerotax) && ($subsection.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.taxratename} ({$subsection.taxrate}%):</span>
                <span class="discount-price">{$subsection.displaytax}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.displayprice}</span>
            </div>
                                {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.displayprice}</span>
            </div>
                                {/if}
                            {/if}
                        {/if}
                        <!-- NOT DIFFERNETTAXRATES -->
                        {if (!$differenttaxrates)}
                            {if (($subsection.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$subsection.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$subsection.discountname}:</span>
                <span class="discount-price">{$subsection.discountvalue}</span>
            </div>
                            {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$subsection.subtotal}</span>
            </div>
                        {/if}
                    {/if}
        </div>
                {/if}
            {/if}
        {/foreach} {* subsections of a section *}
        <!-- sub-sections of order footer component end -->

        <!-- checkboxes inside component start -->
        {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}
            {if $checkbox.showcomponentname==true}
                {if ($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1)}
        <div id="componentrow_{$checkbox.orderlineid}" class="subsection">
			<div class="section-title-header">
			{if ($checkbox.itemcomponentcategoryname != '')}
				<span class="section-category-name">{$checkbox.itemcomponentcategoryname}:</span> <span class="section-category-prompt">{$checkbox.itemcomponentprompt}</span>
			{/if}
			</div>
            <div class="subcheckboxBloc">
                    {if $checkbox.haspreview > 0 }
                <img class="component-preview" src="{$checkbox.componentpreviewsrc|escape}" alt="" />
                <div class="componentSubSectionTitle">
                    {else}
                <div class="componentSubSectionTitleLong">
                    {/if}
                     <div class="componentContentText">
                        <div class="section-title">{$checkbox.itemcomponentname}</div>
                    <!-- START add-edit-change-remove links -->
                    {if $checkbox.haspreview > 0}
                        {if !empty($checkbox.itemcomponentinfo)}
                        <div class="subsection-info">
                            {$checkbox.itemcomponentinfo}
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                        {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo">
                            <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                        </div>
                          {assign var="multilinedesc" value="true"}
                        {/if}
                        {if !empty($checkbox.itemcomponentpriceinfo)}
                        <div class="subsection-info">
                            {$checkbox.itemcomponentpriceinfo}
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                    {else}
                        {if !empty($checkbox.itemcomponentinfo)}
                        <div class="subsection-info-long">
                            {$checkbox.itemcomponentinfo}
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                        {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
                          <div class="subsection-moreinfo-long">
                              <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                          </div>
                          {assign var="multilinedesc" value="true"}
                        {/if}
                        {if !empty($checkbox.itemcomponentpriceinfo)}
                        <div class="subsection-info-long">
                            {$checkbox.itemcomponentpriceinfo}
                        </div>
                             {assign var="multilinedesc" value="true"}
                        {/if}
                    {/if}
                    {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                    ({$checkbox.totalsell})
                    {/if}
                        <div class="clear"></div>
                    </div>
                    {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                        {foreach from=$checkbox.itemcomponentbuttons item=button}
                            <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="-1" data-sectionlineid="{$checkbox.orderlineid}">
                                 <div class="btn-white-left" ></div>
                                 <div class="btn-white-middle {$button.class}">{$button.label}</div>
                                 <div class="btn-white-right"></div>
                             </div>
                            <div class="clear"></div>
                        {/foreach}
                    {/if}
                    <!-- END add-edit-change-remove links -->
                </div>
                    {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}
                <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                        {if $stage=='payment'}
                    <span class="quantityText">{$checkbox.quantity}</span>
                        {else}
                    <input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>
                            {if empty($checkbox.itemqtydropdown)}
                    <input id="itemqty_{$checkbox.orderlineid}" type="text" class="quantity" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                    <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                            {else}
                    <select id="itemqty_{$checkbox.orderlineid}" class="" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">
                                {foreach from=$checkbox.itemqtydropdown item=qtyValue}
                        <option {if $qtyValue==$checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                                {/foreach}
                    </select>
                            {/if}
                        {/if}
                </div>
                    {/if}
                    {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
                <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                    {$checkbox.totalsell}
                </div>
                    {/if}
                    {if $multilinedesc}
                        {assign var="multilinedesc" value="false"}
                    {/if}
                <div class="clear"></div>
            </div>
                    {if (($checkbox.metadatahtml) && ($checkbox.checked))}
            <div id="metadatarow_{$checkbox.orderlineid}" class="subcomponent-metadata{if not $checkbox.checked} invisible{/if}">
                {$checkbox.metadatahtml}
            </div>
                    {/if}
        </div>
        <div class="clear"></div>
                    {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
                        {if $stage=='payment'}
        <div class="line-total">
                            <!-- VALUE OFF TOTAL VOUCHER -->
                            {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
                                {if ($checkbox.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                                {/if}
                                {if (!$showpriceswithtax)}
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                    {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                    {/if}
                                {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    {if ($showtaxbreakdown)}
                                        {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total-small-bottom">{$checkbox.includesitemtaxtext}</div>
                                        {/if}
                                    {/if}
                                {/if}
                            {else}
                                <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                                {if (($differenttaxrates) && ($showpriceswithtax)) }
                                    {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
                                        {if ($checkbox.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                            {/if}
                                        {/if}
                                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                        {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
                                        {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                    {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                        <!-- SHOWTAXBREAKDOWN -->
                                        {if ($showtaxbreakdown)}
                                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total-small-bottom">{$checkbox.includesitemtaxtext}</div>
                                            {/if}
                                        {/if}
                                    {/if}
                                {/if}
                                <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                                {if (($differenttaxrates) && (!$showpriceswithtax)) }
                                    {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
                                        {if ($checkbox.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                            {/if}
                                        {/if}
                                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                        {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
                                    {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                    {else}
                                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                        {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                        {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                        {/if}
                                    {/if}
                                {/if}
                                <!-- NOT DIFFERNETTAXRATES -->
                                {if (!$differenttaxrates)}
                                    {if (($checkbox.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                                    {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                {/if}
                            {/if}
        </div>
                        {/if}
                    {/if}
                {/if}
            {/if}
        {/foreach} {* checkboxes of a section *}
            <!-- checkboxes inside component end -->
        </div>
        {/if}

    {/foreach} {* order footer sections *}

    <!-- orderfooter checkboxes start -->
    {foreach from=$orderfootercheckboxes item=checkbox}
        {if $checkbox.showcomponentname==true}
            {if !isset($bTitleOrder) && (($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1))}

                {if $call_action == 'init'}
<div class="orderFooter" id="orderFooter">
                {/if}

    <div class="sectionLabelLegendFooter">
        {#str_LabelAdditionalItems#}
		<span class="linkToggleFooter">
			{if $stage == 'qty'}
				<span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white">{#str_OrderHide#}</span>
			{else}
				<span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white">{#str_OrderShow#}</span>
			{/if}
		</span>
    </div>
    <div id="contentFooter" {if $stage=='payment'}style="display:none;"{/if}>
                {assign var="bTitleOrder" value="true"}
            {/if}
            {if ($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1)}
        <div id="componentrow_{$checkbox.orderlineid}" class="checkbox">
			<div class="section-title-header">
				{if ($checkbox.itemcomponentcategoryname != '') }
				<span class="section-category-name">{$checkbox.itemcomponentcategoryname}:</span> <span class="section-category-prompt">{$checkbox.itemcomponentprompt}</span>
				{/if}
			</div>

            <div class="checkboxBloc">
                {if $checkbox.haspreview > 0}
                <img src="{$checkbox.componentpreviewsrc|escape}" alt="" class="component-preview" />
                <div class="component-name">
                {else}
                <div class="component-name-long">
                {/if}
                    <div class="componentContentText">
                        <div class="section-title">{$checkbox.itemcomponentname}</div>
                    {if $checkbox.haspreview > 0}
                        {if !empty($checkbox.itemcomponentinfo)}
                        <div class="subsection-info">
                            {$checkbox.itemcomponentinfo}
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                        {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo">
                            <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                        {if !empty($checkbox.itemcomponentpriceinfo)}
                        <div class="subsection-info">
                            {$checkbox.itemcomponentpriceinfo}
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                    {else}
                        {if !empty($checkbox.itemcomponentinfo)}
                        <div class="subsection-info-long">
                            {$checkbox.itemcomponentinfo}
                        </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                        {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo-long">
                            <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                        </div>
                          {assign var="multilinedesc" value="true"}
                        {/if}
                        {if !empty($checkbox.itemcomponentpriceinfo)}
                        <div class="subsection-info-long">
                            {$checkbox.itemcomponentpriceinfo}
                        </div>
                             {assign var="multilinedesc" value="true"}
                        {/if}
                    {/if}
                    {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                        ({$checkbox.totalsell})
                    {/if}
                        <div class="clear"></div>
                    </div>
                    <!-- START add-edit-change-remove links -->
                {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                    {foreach from=$checkbox.itemcomponentbuttons item=button}
                    <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="-1" data-sectionlineid="{$checkbox.orderlineid}">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle {$button.class}">{$button.label}</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                    {/foreach}
                {/if}
                <!-- END add-edit-change-remove links -->
                </div>
                {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}
                <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                    {if $stage=='payment'}
                    <span class="quantityText">{$checkbox.quantity}</span>
                    {else}
                    <input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>
                        {if empty($checkbox.itemqtydropdown)}
                    <input id="itemqty_{$checkbox.orderlineid}" type="text" class="quantity" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                    <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                        {else}
                    <select id="itemqty_{$checkbox.orderlineid}" class="" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" >
                            {foreach from=$checkbox.itemqtydropdown item=qtyValue}
                        <option {if $qtyValue==$checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                            {/foreach}
                    </select>
                        {/if}
                    {/if}
                </div>
                {/if}
                {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
                    <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                        {$checkbox.totalsell}
                    </div>
                {/if}
                 {if $multilinedesc}
                    {assign var="multilinedesc" value="false"}
                {/if}
                <div class="clear"></div>
            </div>
                {if ($checkbox.metadatahtml) && ($checkbox.checked)}
            <div id="metadatarow_{$checkbox.orderlineid}" class="component-metadata{if not $checkbox.checked} invisible{/if}">
                {$checkbox.metadatahtml}
            </div>
                {/if}
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
                {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
                    {if $stage=='payment'}
        <div class="line-total">
                        <!-- VALUE OFF TOTAL VOUCHER -->
                        {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
                            {if ($checkbox.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                            {/if}
                            {if (!$showpriceswithtax)}
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                            {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                {/if}
                            {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                {if ($showtaxbreakdown)}
                                    {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total-small-bottom">{$checkbox.includesitemtaxtext}</div>
                                    {/if}
                                {/if}
                            {/if}
                        {else}

                            <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                            {if (($differenttaxrates) && ($showpriceswithtax)) }
                        {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
                            {if ($checkbox.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                            {/if}
                            {/if}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
                        {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                        {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                                <!-- SHOWTAXBREAKDOWN -->
                                {if ($showtaxbreakdown)}
                                    {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total-small-bottom">{$checkbox.includesitemtaxtext}</div>
                                    {/if}
                                {/if}
                                {/if}

                            {/if}
                            <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                            {if (($differenttaxrates) && (!$showpriceswithtax)) }
                        {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
                            {if ($checkbox.discountvalueraw > 0)}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                            {/if}
                            {/if}
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
                            {/if}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                        {else}
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    {if (($showzerotax) || ( (!$showzerotax) && ($checkbox.displaytaxraw>0) ) )}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.taxratename} ({$checkbox.taxrate}%):</span>
                <span class="discount-price">{$checkbox.displaytax}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                {else}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.displayprice}</span>
            </div>
                                    {/if}
                                {/if}
                            {/if}
                            <!-- NOT DIFFERNETTAXRATES -->
                            {if (!$differenttaxrates)}
                                {if (($checkbox.discountvalueraw > 0) && ($applyVoucherAsLineDiscount))}
            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                <span class="discount-price">{$checkbox.totalsell}</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading">{$checkbox.discountname}:</span>
                <span class="discount-price">{$checkbox.discountvalue}</span>
            </div>
                                {/if}

            <div class="line-sub-total">
                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                <span class="discount-price">{$checkbox.subtotal}</span>
            </div>
                            {/if}
                        {/if}
        </div>
                    {/if}
                {/if}
            {/if}
        {/if}
    {/foreach}
    {if isset($bTitleOrder)}
        {if $stage=='payment'}
        <div class="marginBottomSub"></div>
        {/if}
    </div>
    {/if}

    {if isset($bTitleOrder)}

    <!-- order footer checkboxes end -->
    <div class="contentTotalOrderfooter">
        {if $stage=='qty'}
            {if ($showpriceswithtax == false)}
        <div class="orderfooter-sub-total">
            <span class="total-heading">{#str_LabelOrderFooterTotal#}:</span>
            <span class="order-line-price">{$orderfooteritemstotalsell}</span>
            <div class="clear"></div>
        </div>
            {else}
        <div class="orderfooter-sub-total">
            <span class="total-heading">{#str_LabelOrderFooterTotal#}:</span>
            <span class="order-line-price">{$orderfootertotal}</span>
            <div class="clear"></div>
        </div>
            {/if}
        {/if}
        {* item subtotal row *}
        {if $stage=='payment'}
            {if ($showpriceswithtax == false)}
                {if (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0)))}
                    {if ($differenttaxrates)}
        <div class="orderfooter-sub-total">
            <span class="total-heading">{$orderfootersubtotalname}:</span>
            <span class="order-line-price">{$orderfootersubtotal}</span>
            <div class="clear"></div>
        </div>
                    {/if}
                    {if ($showtaxbreakdown)}
                        {if ($differenttaxrates)}
                            {if ($footertaxratesequal == 1)}
        <div class="orderfooter-sub-total">
            <span class="discount-heading">{$orderfootertaxname} ({$orderfootertaxrate}%):</span>
            <span class="discount-price">{$orderfootertaxtotal}</span>
        </div>
                            {else}
        <div class="orderfooter-sub-total">
            <span class="discount-heading">{$orderfootertaxname}:</span>
            <span class="discount-price">{$orderfootertaxtotal}</span>
        </div>
                            {/if}
                        {/if}
                    {/if}
                {/if}
            {/if}

            {if ($showpriceswithtax == false) && (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0))) && ($differenttaxrates)}
        <div class="orderfooter-sub-total">
            <span class="discount-heading">{$orderfootertotalname}:</span>
            <span class="discount-price">{$orderfootertotal}</span>
        </div>
            {else}
        <div class="orderfooter-sub-total">
            <span class="total-heading">{$orderfootersubtotalname}:</span>
            <span class="order-line-price">{$orderfootersubtotal}</span>
            <div class="clear"></div>
        </div>
            {/if}

            {if ($showpriceswithtax)}
                {if (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0)))}
                    {if ($showtaxbreakdown)}
                        {if ($differenttaxrates)}
        <div class="line-sub-total-small-bottom">
            {$includesorderfootertaxtext}
        </div>
                        {/if}
                    {/if}
                {/if}
            {/if}


        {/if}
    </div>
    {/if}

    {if isset($bTitleOrder) && ($call_action == 'init')}
</div>
    {/if}
{/if} {* end footer total*}