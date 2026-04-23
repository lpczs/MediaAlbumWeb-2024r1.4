<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleOrderStageCreditCardPayment#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/csscustomer_small.css'}" media="screen"/>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>
    </head>
    <body>

		<div id="headerSmall" class="header">
			<div class="headerinside">
				{include file="header_small.tpl"}
			</div>
		</div>

		<div id="contentConfirmation" class="contentScrollCart">

            <div class="contentVisible">
                <p>
                    {$error1}
                    {$error2}
                </p>    
            
            </div> <!-- contentVisible -->

        </div> <!-- contentScrollCart -->

    </body>
</html>