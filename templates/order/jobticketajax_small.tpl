 <!-- QTY PANELS -->

{if $stage == 'qty'}

<div id="contentPanelQty" class="contentLeftPanel">

    <div id="contentLeftScrollQty" class="contentScrollCart">

        <div class="contentVisible">

{/if} {* end {if $stage == 'qty'} *}

{if $stage == 'shipping'}

    {if $isrefreshcall == false}

<div id="contentPanelShipping" class="contentLeftPanel">

    {/if} {* end {if $isrefreshcall == false} *}

    <div id="shippingBack" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnSetHashUrl" data-hash-url="qty">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonBack#}</div>
            <div class="clear"></div>
        </div> <!-- btnDoneTop -->

    </div>

    <div id="contentLeftScrollShipping" class="contentScrollCart">

        <div class="contentVisible">

{/if} {* end {if $stage == 'shipping'} *}

{if $stage == 'payment'}

<div id="contentPanelPayment" class="contentLeftPanel">

    <div id="paymentBack" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnSetHashUrl" data-hash-url="shipping">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonBack#}</div>
            <div class="clear"></div>
        </div> <!-- btnDoneTop -->

    </div>

    <div id="contentLeftScrollPayment" class="contentScrollCart">

        <div class="contentVisible">

{/if} {* end {if $stage == 'payment'} *}

            <div class="pageLabel">

{if $stage=='qty'}
    {#str_LabelNavigationCart#}
{/if}

{if $stage=='shipping'}
    {#str_LabelNavigationShippingBilling#}
{/if}

{if $stage=='payment'}
    {#str_LabelOrderSummaryPayment#}
{/if}

            </div> <!-- pageLabel -->

            <div id="contentHolder">

        <!-- METADATA -->

{if ($metadatalayout != '') && ($stage != 'payment')}

            <div class="contentMetaDataBloc">

                <div class="summaryTab summaryTabMargin">
                    {#str_LabelAdditionalInformation#}
                </div>

                <div class="clear"></div>
                <div>
                    {$metadatalayout}
                </div>

            </div> <!-- contentMetaDataBloc -->

{/if} {* end {if ($metadatalayout != '') && ($stage != 'payment')} *}

        <!-- END METADATA -->


        <!-- ORDER ITEMS -->

{if ($stage=='qty')||($stage=='payment')}

            <div id="orderContent">

            <!-- ORDER LINES START -->

    {foreach from=$orderitems item=orderitem name=orderItemsLoop}

        {include file="$orderline" orderline=$orderitem}

    {/foreach} {* end {foreach from=$orderitems item=orderitem name=orderItemsLoop} *}

            <!-- END ORDER LINES -->

            <!-- ORDER FOOTER START -->

    {include file="$orderfooter"}

            <!-- END ORDER FOOTER -->

            </div> <!-- orderContent -->

{/if} {* end {if ($stage=='qty')||($stage=='payment')} *}

            <!-- END ORDER ITEMS -->

            <!-- PAYMENT SUMMARY SECTION -->

{if $stage == 'payment'}

    {if $metadatalayout != '' }

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab summaryTabMargin">
                    {#str_LabelAdditionalInformation#}
                </div>

                <div class="clear"></div>
                <div class="componentMetadataAdditionnal componentMetadata contentMetadata outerBoxPadding">
                    {$metadatalayout}
                </div>

            </div> <!-- outerBox outerBoxPadding outerBoxWithTab -->

    {/if} {* end {if $metadatalayout != '' } *}

            <!-- SHIPPING SUMMARY DETAILS -->

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab">
                    {#str_TitleOrderStageShipping#}
                </div>
                <div class="clear"></div>

                <div class="content contentPayment ">

                    <div class="contentAddress">

                        <div class="contentAddressBody">

                            <div class="shippingSummary outerBoxPadding">
                                <div class="shippingTitle">
                                    {$shippingStoreAddressLabel}
                                </div>
                                <div class="shippingText">
                                    {$shippingaddress}
                                </div>
                            </div> <!-- shippingSummary -->

                            <div class="billingSummary outerBoxPadding">
                                <div class="shippingTitle">
                                    {#str_LabelBillingAddress#}
                                </div>
                                <div class="shippingText">
                                    {$billingaddress}
                                </div>
                            </div> <!-- billingSummary -->

                            <div class="clear"></div>
                        </div> <!-- contentAddressBody -->

                    </div> <!-- contentAddress -->

                    <div class="itemSection outerBoxPadding">

                        <div id="shippingLabel" class="itemSectionLabel itemSectionTotalLabel">
                            {#str_LabelOrderShipping#} ({$shippingmethodname}):
                        </div>

                        <div id="shippingPrice" class="itemTotalNumber">
                            {$ordertotalshipping}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                </div> <!-- content contentPayment -->

                <div class="shippingTotalDetails">

                    <!-- VOUCHER -->

    {if ($vouchersection == 'SHIPPING') || (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && (!$specialvouchertype))}

                    <!-- SHIPPING VOUCHER  -->

        {if $vouchersection == 'SHIPPING'}

            {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {$shippingdiscountname}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

            {/if} {* {if $shippingdiscountvalueraw > 0} *}

                    <!-- SHOW SHIPPING TAX  -->

            {if $showshippingtax}

                    <!-- SHOW PRICES WITH TAX  -->

                {if $showpriceswithtax}

                    {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountedvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if $shippingdiscountvalueraw > 0} *}

                    <div class="line-sub-total-small-bottom">
                        {$includesshippingtaxtext}
                    </div>

                {else} {* else {if $showpriceswithtax} *}

                    {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelSubTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountedvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if $shippingdiscountvalueraw > 0} *}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {$shippingtaxname} ({$shippingtaxrate}%):
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtaxtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if $showpriceswithtax} *}

            {else} {* else {if $showshippingtax} *}

                {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if $shippingdiscountvalueraw > 0} *}

            {/if} {* end {if $showshippingtax} *}

        {/if} {* end {if $vouchersection == 'SHIPPING'} *}

                    <!-- TOTAL VOUCHER  -->

        {if (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && (!$specialvouchertype))}

            {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {$shippingdiscountname}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

            {/if} {* end {if $shippingdiscountvalueraw > 0} *}

                    <!-- SHOW SHIPPING TAX  -->

            {if $showshippingtax}

                    <!-- SHOW PRICES WITH TAX  -->

                {if $showpriceswithtax}

                    {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountedvalue}
                        </div>

                        <div class="clear"></div>
                    </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if $shippingdiscountvalueraw > 0} *}

                    <div class="line-sub-total-small-bottom">
                        {$includesshippingtaxtext}
                    </div>

                {else} {* else {if $showpriceswithtax} *}

                    {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelSubTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountedvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if $shippingdiscountvalueraw > 0} *}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {$shippingtaxname} ({$shippingtaxrate}%):
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtaxtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if $showpriceswithtax} *}

            {else} {* else {if $showshippingtax} *}

                {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if $shippingdiscountvalueraw > 0} *}

            {/if}  {* end {if $showshippingtax} *}

        {/if} {* end {if (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && (!$specialvouchertype))} *}

    {else} {* else {if ($vouchersection == 'SHIPPING') || (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && (!$specialvouchertype))} *}

        {if ((($vouchersection == 'TOTAL') && ($differenttaxrates == true) && ($specialvouchertype)) || (($vouchersection == 'TOTAL') && ($differenttaxrates == false) && ($applyVoucherAsLineDiscount == true)))}

            {if $shippingdiscountvalueraw > 0}

                    <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            {$shippingdiscountname}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {if ($showpriceswithtax)}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountedvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {else} {* else {if ($showpriceswithtax)} *}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelSubTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingdiscountedvalue}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if ($showpriceswithtax)} *}

            {/if} {* end {if $shippingdiscountvalueraw > 0} *}

                    <!-- SHOW SHIPPING TAX  -->

            {if $showshippingtax}

                    <!-- SHOW PRICES WITH TAX  -->

                {if $showpriceswithtax}

                    <div class="line-sub-total-small-bottom">
                        {$includesshippingtaxtext}
                    </div>

                {else} {* else {if $showpriceswithtax} *}

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {$shippingtaxname} ({$shippingtaxrate}%):
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtaxtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>

                        <div class="itemTotalNumber">
                            {$shippingtotal}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if $showpriceswithtax} *}

            {/if} {* end {if $showshippingtax} *}

        {else} {* else {if (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && ($specialvouchertype))} *}

            <!-- SHOW SHIPPING TAX  -->

            {if $showshippingtax}

                <!-- SHOW PRICES WITH TAX  -->

                {if $showpriceswithtax}

                    <div class="line-sub-total-small-bottom">
                        {$includesshippingtaxtext}
                    </div>

                {else} {* else {if $showpriceswithtax} *}

                    <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            {$shippingtaxname} ({$shippingtaxrate}%):
                        </div>
                        <div class="itemTotalNumber">
                            {$shippingtaxtotal}
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            {#str_LabelOrderShippingTotal#}:
                        </div>
                        <div class="itemTotalNumber">
                            {$shippingtotal}
                        </div>
                        <div class="clear"></div>
                    </div>

                {/if} {* end {if $showpriceswithtax} *}

            {/if} {* end {if $showshippingtax} *}

        {/if} {* end {if (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && ($specialvouchertype))} *}

    {/if} {* end {if ($vouchersection == 'SHIPPING') || (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && (!$specialvouchertype))} *}

                </div> <!-- shippingTotalDetails -->

            </div> <!-- outerBox outerBoxWithTab -->

            <!-- END SHIPPING SUMMARY DETAILS -->

            <!-- VOUCHERS -->

    {if $showvouchers == true}

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab">
                    {#str_SectionTitleVouchers#}
                </div>
                <div class="clear"></div>

                <div class="orderSummaryColor outerBoxPadding" id="ordertotalsummary">

                    {#str_LabelEnterOrderVoucher#}<br /><br />

        {if $voucherstatus != ''}

                    <b>{$voucherstatus}</b><br /><br />

        {/if} {* end {if $voucherstatus != ''} *}

        {if ($defaultdiscountactive == false) && ($vouchercode != '')}
                    <div class="formLine2">
                        <input type="text" id="vouchercode" name="vouchercode" placeholder="{#str_LabelVoucherCode#}" data-trigger="keyup" data-decorator="fnForceUpperAlphaNumeric" class="voucherinput" value="{$vouchercode}" readonly="readonly" />
                    </div>
                    <div class="clear"></div>
        {else}
                    <div class="formLine2">
                        <input type="text" id="vouchercode" name="vouchercode" placeholder="{#str_LabelVoucherCode#}" data-trigger="keyup" data-decorator="fnForceUpperAlphaNumeric" class="voucherinput" value="" />
                    </div>
                    <div class="clear"></div>
        {/if}

        {if ($vouchercode == '') || (($vouchercode != '') && ($defaultdiscountactive == true))}

                    <div id="setvoucher">

                        <div class="btnAction btnRedeem" data-decorator="setVoucher">
                            <div class="btnBlueContent">{#str_LabelRedeem#}</div>
                        </div>
                        <div class="clear"></div>

                    </div> <!-- setvoucher -->

        {/if} {* end {if ($vouchercode != '') && ($defaultdiscountactive == false)} *}

        {if ($vouchercode != '') && ($defaultdiscountactive == false)}

                    <div id="removevoucher">

                       <div class="btnAction btnRedeem" data-decorator="removeVoucher">
                            <div class="btnBlueContent">{#str_LabelRemove#}</div>
                        </div>
                        <div class="clear"></div>

                    </div> <!-- removevoucher -->

        {/if} {* end {if ($vouchercode != '') && ($defaultdiscountactive == false)} *}

                </div> <!-- orderSummaryVoucher -->

            </div> <!-- outerBox outerBoxWithTab -->

    {/if} {* end {if $showvouchers == true} *}

            <!-- END VOUCHERS -->

            <!-- GIFT CARDS -->

    {if $showgiftcardsbalance == true}

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab">
                    {#str_SectionTitleGiftCards#}
                </div>
                <div class="clear"></div>

                <div class="orderSummaryColor outerBoxPadding">

                    {#str_LabelEnterOrderGiftCard#}<br /><br />

        {if $giftcardstatus != ''}

                    <b>{$giftcardstatus}</b><br /><br />

        {/if} {* end {if $giftcardstatus != ''} *}

                    <div class="formLine2">
                        <input type="text" id="giftcardcode" name="giftcardcode" placeholder="{#str_LabelGiftCardCode#}" value="" class="voucherinput" data-trigger="keypress" data-decorator="fnForceUpperAlphaNumeric" />
                    </div>
                    <div class="clear"></div>

                    <div id="setgiftcard">
                        <div class="btnAction btnRedeem" data-decorator="setGiftCard">
                            <div class="btnBlueContent">{#str_LabelRedeem#}</div>
                        </div>
                        <div class="clear"></div>
                    </div> <!-- setgiftcard -->

                </div> <!-- orderSummaryGiftCards -->

            </div> <!-- contentGiftCardsBloc -->

    {/if} {* end {if $showgiftcardsbalance == true} *}

            <!-- END GIFT CARDS -->

            <!-- SUMMARY SUBTOTAL -->

            <div class="totalLineDetail outerBoxPadding">

    {if ((($vouchersection == 'TOTAL') && ($differenttaxrates == false)) || (($vouchersection == 'TOTAL') && ($differenttaxrates) && ($specialvouchertype)))}

        {if ($vouchersection == 'TOTAL') && ($differenttaxrates==false)}

            {if ($applyVoucherAsLineDiscount == true)}

                <div>
                    <div class="totalLabel">
                        {#str_LabelOrderSubTotal#}:
                    </div>
                    <div class="totalNumber">
                        {$ordersubtotal}
                    </div>
                    <div class="clear"></div>
                </div>

            {else}

                <div>
                    <div class="totalLabel">
                        {#str_LabelOrderSubTotal#}:
                    </div>
                    <div class="totalNumber">
                        {$orderbeforediscounttotalvalue}
                    </div>
                    <div class="clear"></div>
                </div>

                <div>
                    <div class="totalLabel">
                        {$orderaftertotaldiscountname}:
                    </div>
                    <div class="totalNumber">
                        {$ordertotaldiscountvalue}
                    </div>
                    <div class="clear"></div>
                </div>

            {/if}

        {/if} {* end {if ($vouchersection == 'TOTAL') && ($differenttaxrates==false)} *}

    {else} {* else {if ((($vouchersection == 'TOTAL') && ($differenttaxrates == false)) || (($vouchersection == 'TOTAL') && ($differenttaxrates) && ($specialvouchertype)))} *}

        {if ($differenttaxrates == false) && ($showpriceswithtax == false) && (($hastotaltax == true) || ($showzerotax == true))}

                <div>
                    <div class="totalLabel">
                        {#str_LabelOrderSubTotal#}:
                    </div>
                    <div class="totalNumber">
                        {$ordersubtotal}
                    </div>
                    <div class="clear"></div>
                </div>

        {/if} {* end {if ($differenttaxrates == false) && ($showpriceswithtax == false) && (($hastotaltax == true) || ($showzerotax == true))} *}

    {/if} {* end {if ((($vouchersection == 'TOTAL') && ($differenttaxrates == false)) || (($vouchersection == 'TOTAL') && ($differenttaxrates) && ($specialvouchertype)))} *}

                <div>
                   <div class="totalLabel">
                       {#str_LabelOrderShippingCost#}:
                   </div>
                   <div class="totalNumber">
                       {$ordershippingcost}
                   </div>
                   <div class="clear"></div>
               </div>

    {if ($differenttaxrates == false) && ($showpriceswithtax == false) && (($hastotaltax == true) || ($showzerotax == true))}

                <div>
                    <div class="totalLabel">
                        {$itemtaxname} ({$itemtaxrate}%):
                    </div>
                    <div class="totalNumber">
                        {$ordertotaltax}
                    </div>
                    <div class="clear"></div>
                </div>

    {/if} {* end {if ($differenttaxrates == false) && ($showpriceswithtax == false) && (($hastotaltax == true) || ($showzerotax == true))} *}

    {if $ordergiftcardtotal > 0 || $disabled_giftcard == 'disabled'}

                <div>
                    <div class="totalLabel">
                        {#str_LabelOrderTotal#}:
                    </div>
                    <div class="totalNumber">
                        {$ordertotal}
                    </div>
                    <div class="clear"></div>
                </div>

        {if ((($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true)) && ($includestaxtotaltext != ''))}

                <div {if ($ordergiftcardtotal == 0 || $disabled_giftcard == 'disabled') } style="display:none" {/if} class="line-sub-total-small" id="includetaxtextwithgiftcard">
                    {$includestaxtotaltext}
                </div>

        {/if} {* end {if ((($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true)) && ($includestaxtotaltext != ''))} *}

                <div>
                    <div class="totalLabel">
                        <span id="giftbutton" title="{$tooltipGiftcardButton}" class="classGift{$add_delete_giftcard}" data-decorator="changeGiftCard"></span>

                        <span id="giftcard" class="{$disabled_giftcard}">
                            {#str_LabelGiftCard#}
                        </span>
                    </div>

                    <div class="totalNumber {$disabled_giftcard}" id="giftcardamount">
                        {$ordergiftcardtotalvalue}
                    </div>
                    <div class="clear"></div>

                </div> <!-- giftcard -->

    {/if} {* end {if $ordergiftcardtotal > 0} *}

            </div> <!-- totalLineDetail outerBoxPadding -->

            <!-- END SUMMARY SUBTOTAL -->

            <!-- AMOUNT TO PAY -->

            <div class="amountToPay outerBoxPadding">

                <div class="totalLabel">
                    {#str_LabelAmountToPay#}:
                </div>

                <div class="totalNumber" id="ordertotaltopayvalue">
                    {$ordertotaltopayvalue}
                </div>
                <div class="clear"></div>

            </div> <!-- amountToPay -->

    {if (($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true))}

            <div {if $ordergiftcardtotal > 0 && $disabled_giftcard != 'disabled'} style="display:none" {/if} class="line-sub-total-small-bottom" id="includetaxtextwithoutgiftcard">
                {$includestaxtotaltext}
            </div>

    {/if} {* end {if (($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true))} *}

            <!-- END AMOUNT TO PAY  -->

            <!-- PAYMENT METHODS LIST -->

            <div {if $hidepayments} style="display:none" {/if} id="paymenttableobj" class="contentPaymentMethod">

                <div class="summaryTab summaryTabMargin">
                    {#str_LabelPaymentMethod#}
                </div>

                <div class="clear"></div>
                <div id="paymentMethodsList">
                    {$paymentmethodslist}
                </div>

            </div> <!-- contentPaymentMethod -->

            <!-- END PAYMENT METHODS LIST -->

    {if $showtermsandconditions == 1}

            <div id="ordertermsandconditionscontainer" class="outerBox outerBoxPadding outerBoxMarginTop">

                <input class="inputTermsAndConditions" type="checkbox" name="ordertermsandconditions" id="ordertermsandconditions" data-decorator="acceptTermsAndConditions">
                <label class="labelTermsAndConditions" for="ordertermsandconditions" id="labelTermsAndConditions">{#str_LabelTermsAndConditionsAgreement#} <a id="ordertermsandconditionslink" href="#" data-decorator="orderTermsAndConditions" class="termsAndConditionsLink">{#str_TitleTermsAndConditions#}</a></label>
                <div class="clear"></div>

            </div><!-- termsAndConditions -->

    {/if} {* end {if $showtermsandconditions == 1} *}

{/if} {* end {if $stage == 'payment'} *}

            <!-- END PAYMENT SUMMARY SECTION -->

            <!-- SHIPPING PANEL -->

{if $stage == 'shipping'}

            <div id="addressHolder" class="shippingContent">

                <div class="outerBox">

                    <div class="outerBoxPadding">

                        <div class="shippingTitle">
                            {#str_LabelShippingMethod#}
                        </div>

                        <div id="shippingSelectedName" class="shippingText">
                            {$shippingmethodselectedlabel}<br />
                            <span class="methodPrice">{$shippingselectedrate}</span>
                        </div>

                    </div>

                    <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="changeMethod">

                        <div class="changeBtnText">
                            {#str_ButtonChange#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding outerBoxPadding -->

                </div> <!-- outerBox outerBoxMarginTop -->

                <div id="shippingAddress" class="outerBox outerBoxMarginTop">

                    <div class="outerBoxPadding">

                        <div id="labelShippingStoreAddress" class="shippingTitle">
                            {$initialShippingStoreAddressLabel}
                        </div>
                        <div id="shippingStoreAddress" class="shippingText">
                            {if (($collectFromStoreCode != '') || ($collectFromStore!=1))}
                                {$initialShippingStoreAddress}
                            {/if}
                        </div>

                    </div>

    {if (($canmodifyshipping == true) || ($canmodifybilling == true) || ($optionCFS))}

        {if $canmodifyshipping == true}

                    <div id="changeShippingDiv" class="contentChangeBtn outerBoxPadding" {if $collectFromStore==1}style="display:none"{/if} data-decorator="fnSetHashUrl" data-hash-url="changeShippingAddress">

                        <div id="changeshipping" class="changeBtnText">
                            {#str_ButtonChange#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

        {/if} {* end {if $canmodifyshipping == true} *}

        {if $storeisfixed == 0}
                    <input type="hidden" id="shippingcfscontact" name="shippingcfscontact" value="{if $collectFromStore == 0}0{else}1{/if}" />

                    <div id="selectStoreDiv" class="contentChangeBtn outerBoxPadding" {if $collectFromStore == 0}style="display:none"{/if} data-decorator="fnSetHashUrl" data-hash-url="store|{$shippingmethodcfscode}">

                        <div id="selectStoreButton" class="changeBtnText">
                            {#str_ButtonSelectStore#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

                    <div id="editStoreContactDetailsDiv" class="contentChangeBtn outerBoxPadding" {if (($collectFromStore == 0) || ($collectFromStoreCode == ''))}style="display:none"{/if} data-decorator="fnSetHashUrl" data-hash-url="changeShippingAddressContactDetails">

                        <div id="editStoreContactDetailsButton" class="changeBtnText">
                            {#str_ButtonEditCollectionDetails#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

        {/if} {* end {if $storeisfixed == 0} *}

    {/if} {* end {if (($canmodifyshipping == true) || ($canmodifybilling == true) || ($optionCFS))} *}

                </div> <!-- outerBox outerBoxMarginTop -->

                <div id="billingAddress" class="outerBox outerBoxMarginTop">

                    <div class="outerBoxPadding">

                        <div class="shippingTitle">
                            {#str_LabelBillingAddress#}
                        </div>

                        <div class="shippingText">

                            <div id="sameasshippingaddressobj" {if (($canmodifyshipping==false)||($canmodifybilling==false))}style="display:none"{/if} class="shippingSameAddress">
                                <input type="checkbox" class="sameasshippingaddress" id="sameasshippingaddress" name="sameasshippingaddress" {if ($sameshippingandbillingaddress==true)}checked="checked"{/if} data-decorator="setSameAddress" {if ($collectFromStore==1)}disabled="disabled"{/if} />
                                <label for="sameasshippingaddress" {if ($collectFromStore==1)}class="disabled"{/if}>
                                    {#str_LabelSameAsShippingAddress#}
                                </label>
                            </div>

                            <div>
                                {$billingaddress}
                            </div>

                        </div> <!-- shippingText -->

                    </div>

                    {if (($sameshippingandbillingaddress==true) && ($canmodifyshipping==true))}

                    <div class="contentChangeBtn outerBoxPadding" id="changebilling"  disabled="disabled" {if ($canmodifybilling==false)}style="display:none"{/if}>
                        <div id="changeBillingLabel" class="changeBtnText disabled">
                            {#str_ButtonChange#}
                        </div>

                        <div id="changeBillingImage" class="changeBtnImg disabled">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div><!-- contentChangeBtn outerBoxPadding -->

                    {else}

                     <div class="contentChangeBtn outerBoxPadding" id="changebilling" {if ($canmodifybilling==false)}style="display:none"{/if} data-decorator="showChangeBillingAddress">
                        <div id="changeBillingLabel" class="changeBtnText">
                            {#str_ButtonChange#}
                        </div>

                        <div id="changeBillingImage" class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div><!-- contentChangeBtn outerBoxPadding -->

                    {/if}

                </div> <!-- outerBox outerBoxMarginTop -->

            </div> <!-- addressHolder -->

            <div class="totalLineDetail outerBoxPadding">

                <div>
                    <div class="totalLabel">
                        {#str_LabelOrderSubTotal#}:
                    </div>
                    <div class="totalNumber">
                        {$orderitemstotalsell}
                    </div>
                    <div class="clear"></div>
                </div>

                <div>
                    <div class="totalLabel">
                        {#str_labelShippingTotal#}:
                    </div>
                    <div class="totalNumber">
                        {$ordershippingcost}
                    </div>
                    <div class="clear"></div>
                </div>

            </div> <!-- totalLineDetail outerBoxPadding -->

{/if} {* end {if $stage == 'shipping'} *}

            <!-- END SHIPPING PANEL -->

            <!-- TOTAl SECTION -->

{if $stage == 'shipping'}

            <div class="total">

                <div class="totalLabel">{#str_labelTotal#}:</div>
                <div class="totalNumber" id="orderTotal">
                    {$ordertotal}
                </div>
                <div class="clear"></div>

            </div> <!-- total -->

{else} {* else {if $stage == 'shipping'} *}

    {if $stage == 'qty'}

            <div class="total">
                <div class="totalLabel">{#str_labelTotal#}:</div>
                <div class="totalNumber" id="orderTotal">
                    {$ordertotal}
                </div>
                <div class="clear"></div>
            </div> <!-- total -->

            <div class="quantityTotalNotice">
                {#str_LabelTotalNotice#}
            </div>

    {/if} {* end {if $stage == 'qty'} *}

{/if} {* end {if $stage == 'shipping'} *}

            <div class="buttonBottomSection">

{if $stage=='qty'}

                <div class="btnAction btnContinue" data-decorator="acceptDataEntry">
                    <div class="btnContinueContent">{#str_ButtonContinue#}</div>
                </div>

{else} {* else {if $stage=='qty'} *}

    {if $stage=='payment'}

                <div id="ordercontinuebutton" data-decorator="orderButtonCompleteOrder">
                    <div id="btnConfirm" class="btnAction btnContinue">
                        <div id="btnContinueContentFinal" class="btnContinueContent">{#str_ButtonConfirmOrder#}</div>
                    </div>
                </div>

    {else} {* else {if $stage=='payment'} *}
                <div data-decorator="acceptDataEntry">
                    <div class="btnAction btnContinue">
                        <div id="btnContinueContent" class="btnContinueContent">{#str_ButtonContinue#}</div>
                    </div>
                </div>

    {/if} {* end {if $stage=='payment'} *}

{/if} {* end {if $stage=='qty'} *}

                <div class="linkAction" data-decorator="cancelOrderConfirmation">
                    <div class="deleteBtnText">{#str_ButtonCancelOrder#}</div>
                </div>

            </div> <!-- buttonBottomSection -->

        </div> <!--  contentHolder -->

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

{if $stage == 'shipping'}

    {if $isrefreshcall == false}

</div> <!-- contentLeftPanel -->

    {/if} {* end {if $isrefreshcall == false} *}

{else} {* else {if $stage == 'shipping'} *}

</div> <!-- contentLeftPanel -->

{/if} {* end {if $stage == 'shipping'} *}

{if $stage == 'qty'}

<div id="contentPanelComponent" class="contentRightPanel">

    {foreach from=$orderitems item=orderitem}

    <div id="componentContainer{$orderitem.orderlineid}" style="display:none;">

        {include file="order/componentdetail_small.tpl" orderline=$orderitem}

    </div>

    {/foreach} {* end {foreach from=$orderitems item=orderitem} *}

    <div id="componentContainer-1" style="display:none;">

        {include file="order/orderfootercomponentdetail_small.tpl"}

    </div>

</div> <!-- contentRightPanel -->


<div id="contentPanelSubComponent" class="contentRightPanel">

    {foreach from=$orderitems item=orderitem}

    <div id="subcomponentContainer{$orderitem.orderlineid}" style="display:none;">

        {include file="order/subcomponentdetail_small.tpl" orderline=$orderitem}

    </div>

    {/foreach} {* end {foreach from=$orderitems item=orderitem} *}

    <div id="subcomponentContainer-1" style="display:none;">

       {include file="order/orderfootersubcomponentdetail_small.tpl"}

   </div>

</div> <!-- contentRightPanel -->

<div id="contentPanelComponentChoice" class="contentRightPanel">

    <div id="contentNavigationChoice" class="contentNavigation">

        <div class="buttonTopSection">

            <div class="btnLeftSection" id="choiceBackButton">
                <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
                <div class="btnDone">{#str_ButtonCancel#}</div>
                <div class="clear"></div>
            </div>

            <div id="updateChoiceBtn" class="btnRightSection">
                <div class="btnUpdate">{#str_ButtonUpdate#}</div>
            </div>
            <div class="clear"></div>

        </div> <!-- buttonTopSection -->

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollChoice" class="contentScrollCart">
        <div id="contentVisibleChoice" class="contentVisible"></div>
    </div>

</div> <!-- contentPanelCompenentChoice -->

{/if} {* end {if $stage == 'qty'} *}

{if $stage == 'shipping'}

    {if $isrefreshcall == false}

<!-- SHIPPING PANELS -->

<div id="contentPanelMethodList" class="contentRightPanel">

    
    {include file='order/shippingmethodlist.tpl'}
    <!-- END SHIPPING METHOD LIST -->

</div> <!-- contentRightPanel -->

<div id="contentPanelSelectStore" class="contentRightPanel">

</div> <!-- contentPanelSelectStore -->

<div id="contentPanelUpdateAddress" class="contentRightPanel">

</div> <!-- contentPanelSelectStore -->

<!-- END SHIPPING PANELS -->

    {/if} {* end {if $isrefreshcall == false} *}

{/if} {* end {if $stage == 'shipping'} *}

{if $stage == 'payment'}

<div id="contentPanelPaymentgateway" class="contentRightPanel">
</div> <!-- contentPanelPaymentGateway -->

<div id="contentPanelConfirmation" class="contentRightPanel">
</div> <!-- contentPanelConfirmation -->

{/if} {* end {if $stage == 'payment'} *}