{if $isajaxcall == false}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleOrderConfirmation#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        <script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>
        <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/csscustomer_small.css'}" media="screen"/>
        {include file="order/finalstageincludedecorator.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

            {include file="order/orderconfirmation.tpl"}

            //]]>
        </script>

    </head>
    <body>
    {if $googletagmanagercccode ne ''}
        <script type="text/javascript" {$nonce}>
            dataLayer.push({ ecommerce: null });
            {literal}
            dataLayer.push({
                event: "purchase",
                ecommerce: {
                    transaction_id: "{/literal}{$ordernumber}{literal}",
                    value: {/literal}{$orderdata.total}{literal},
                    tax: {/literal}{$orderdata.ordertaxtotal}{literal},
                    shipping: {/literal}{$orderdata.shippingtotal}{literal},
                    currency: "{/literal}{$orderdata.ordercurrency}{literal}",
                    {/literal}
                    {foreach from=$orderdata.orderlines item=line}
                    {literal}
                    items: [
                        {
                            item_id: "{/literal}{$ordernumber}{literal}",
                            item_name: "{/literal}{$line.productname}{literal}",
                            affiliation: "{/literal}{$orderdata.brandcode}{literal}",
                            price: {/literal}{$line.price}{literal},
                            quantity: "{/literal}{$line.qty}{literal}"
                        },
                        {/literal}
                        {/foreach}
                        {literal}
                    ]
                }
            });
        </script>
    {/literal}
    {/if}
        <div id="headerSmall" class="header">
            <div class="headerinside">
                {include file="header_small.tpl"}
            </div>
        </div>

{/if}{* end {if $isajaxcall == false} *}

        <div id="contentConfirmation" class="contentScrollCart">

            <div class="contentVisible">

                <div class="pageLabel">

                {#str_TitleOrderConfirmation#}

                </div>

                <div class="orderInformationBloc outerBox outerBoxPadding">
                    <div>
                        {#str_MessageOrderConfirmation2#}
                    </div>
                    <div class="orderInformationMessage">
                        {$str_MessageOrderConfirmation1}
                    </div>

                    {if $cciCompletionMessage != ""}

                        <div class="orderInformationMessage">
                            {#str_MessageOrderConfirmation3#}
                        </div>

                        <div>
                            {$cciCompletionMessage}
                        </div>

					{elseif $additionalPaymentinfo != ""}

						<div class="orderInformationMessage">
                            {#str_MessageOrderConfirmation3#}
                        </div>

						<div class="orderInformationMessage">
							{$additionalPaymentinfo}
						</div>

                    {else}

                        <div class="orderInformationMessage">
                            {#str_MessageOrderConfirmation3#}
                        </div>

                    {/if} {* end {if $cciCompletionMessage != ""} *}

                </div>

                {if $mainwebsiteurl != ''}

                <div class="buttonBottomSection">

                    <div data-decorator="fnRedirect" data-url="{$mainwebsiteurl|escape}">
                        <div class="btnAction btnContinue">
                            <div class="btnContinueContent">{#str_ButtonContinue#}</div>
                        </div>
                    </div>

                </div>
                <div class="clear"></div>

                {/if}{* end {if $mainwebsiteurl != ''} *}

            </div> <!-- contentVisible -->

        </div> <!-- contentScrollCart -->

{if $isajaxcall == false}

    </body>

</html>

{/if}