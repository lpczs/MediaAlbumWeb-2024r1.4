<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_MessageTransferring#}</title>
        
        <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/csscustomer_small.css'}" media="screen"/>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

			{include file="order/PaymentIntegration/PaymentReturn.tpl"}

			{literal}

			function removeLanguage()
			{
				document.getElementById('languageSection').style.display = 'none';
			}

			window.addEventListener('DOMContentLoaded', function(event) {
				removeLanguage();
			});
			{/literal}

            //]]>
        </script>
    </head>
    <body>

		<div id="headerSmall" class="header">
			<div class="headerinside">
				{include file="header_small.tpl"}
			</div>
		</div>

        <div class="paymentContent">
            <div class="paymentMessage">
                {#str_MessageTransferring#}<br />
                {#str_MessagePleaseWait#}
            </div>
            <div id="progress" class="paymentProgressBar"></div>
        </div>

        <form id="paymentform" name="paymentform" action="{$server}" method="post" accept-charset="utf-8">

            {foreach from=$parameter key=name item=value}

                <input type="hidden" name="{$name}" value="{$value}">

            {/foreach}

        </form>
    </body>
</html>