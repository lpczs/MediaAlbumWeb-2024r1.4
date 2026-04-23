<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_MessageTransferring#}</title>
        
        <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/csscustomer_large.css'}" media="screen"/>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>
		{if $ispaypalplus == 'true'}
		<script type="text/javascript" src="{$webroot}/PaymentIntegration/PayPalPlus/ppplus.min.js" {$nonce}></script>
		{/if}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

           {include file="order/PaymentIntegration/PaymentRequest.tpl"}

            window.onload = function() 
            { 
                initializePaymentGateway();
            }; 

            //]]>
        </script>

    </head>
    <body>
        <div class="outer-page{if $sidebarleft != ''} fullsize-outer-page{/if}">
            <div id="header" class="headerScroll">
                <div class="headerinside">
                    {include file="header_large.tpl"}
                </div>
            </div>

			<div class="contentScrollCartNoNavigation">
				<table id="requesttable" width="100%">
					<tr align="center"><td id="transferring" class="paymentMessage">{#str_MessageTransferring#}<p></td></tr>
					<tr align="center"><td class="paymentMessage">{#str_MessagePleaseWait#}<p></td></tr>
					<tr align="center"><td><img id="progress" src="{$brandroot}/images/progress.gif" style="visibility:hidden"></td></tr>
				</table>

				<form id="requestform" name="requestform" action="" method="{$method}" accept-charset="utf-8">
					{foreach from=$parameter key=name item=value}
						<input type="hidden" name="{$name}" value="{$value}">
					{/foreach}
				</form>
			</div>
		</div>
    </body>
</html>