<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleOrderStageCreditCardPayment#}</title>
		
        <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/csscustomer_large.css'}" media="screen"/>
		<script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
		<script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/listeners.js'}" {$nonce}></script>

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

            {include file="order/PaymentIntegration/PaymentRequest.tpl"}

            function submitForm()
            {
                document.submitform.submit();
            };

            window.addEventListener('DOMContentLoaded', function(event) {
                document.body.addEventListener('click', decoratorListener);
            });
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
				<form id="submitform" name="submitform" method="POST" accept-charset="utf-8" action="{$homeurl}">
					<table align="center" border="0" cellpadding="5" cellspacing="0" width="800" style="background-color: #FFFFFF; border-top-width:1px; border-bottom-width:1px; border-left-width:1px; border-right-width:1px; border-style:solid; empty-cells:show">
						<tr><td class="text3" align="center" style="color:#FF0000" width="800">{#str_ErrorPaymentFailed1#}</td></tr>
						<tr><td class="text" align="center" width="700">&nbsp;</td></tr>
						<tr><td class="text1" align="center" width="700">{#str_ErrorPaymentFailed2#}</td></tr>
						<tr><td class="text" align="center" width="700">{$data1}</td></tr>
						<tr><td class="text" align="center" width="700">{$data2}</td></tr>
						<tr><td class="text" align="center" width="700">{$data3}</td></tr>
						<tr><td class="text" align="center" width="700">{$data4}</td></tr>
						<tr><td class="text3" align="center" width="700">&nbsp;</td></tr>
					</table>

					<div class="btnRightContinue">
						<div class="contentBtn" data-decorator="submitForm">
							<div class="btn-green-left" ></div>
							<div class="btn-green-middle">{#str_ButtonContinue#}</div>
							<div class="btn-accept-right"></div>
						</div>
					</div>
					<div class="clear"></div>

					<input type="HIDDEN" id="ref" name="ref" value="{$ref}">
					<input type="HIDDEN" id="fsaction" name="fsaction" value="Order.ccResume">
				</form>
			</div>
		</div>
	</body>
</html>