{if ($displayInline == false) || (! isset($displayInline))}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleOrderStageCreditCardPayment#}</title>
		
        <link rel="stylesheet" type="text/css" href="{$brandroot}{asset file='/css/csscustomer_small.css'}" media="screen"/>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>
        <script type="text/javascript" src="{$webroot}{asset file='/utils/listeners.js'}" {$nonce}></script>
		<script type="text/javascript" {$nonce}>
			//<![CDATA[
			function submitForm()
			{
				{if $displayInline === true}
						document.submitform[0].submit();
				{else}
					document.submitform.submit();
				{/if}
			};

			window.addEventListener('DOMContentLoaded', function(event) {
				document.body.addEventListener('click', decoratorListener);
			});
			//]]>
		</script>
    </head>
    <body>

		<div id="headerSmall" class="header">
			<div class="headerinside">
				{include file="header_small.tpl"}
			</div>
		</div>
{/if}

		<div id="contentConfirmation" class="contentScrollCart">

            <div class="contentVisible">

				<div class="pageLabel">

                {#str_ErrorPaymentFailed1#}

                </div>

                <div class="orderInformationBloc outerBox outerBoxPadding">
                    <div>
                        {#str_ErrorPaymentFailed2#}
                    </div>
                    <div class="orderInformationMessage">
                        {$data1}
                    </div>
					<div class="orderInformationMessage">
                        {$data2}
                    </div>
					<div class="orderInformationMessage">
                        {$data3}
                    </div>
					<div class="orderInformationMessage">
                        {$data4}
                    </div>
                </div>

               <form id="submitform" name="submitform" method="POST" accept-charset="utf-8" action="{$homeurl}">

					<div class="buttonBottomSection">

						<div data-decorator="submitForm">
							<div class="btnAction btnContinue">
								<div class="btnContinueContent">{#str_ButtonContinue#}</div>
							</div>
						</div>

					</div>
					<div class="clear"></div>

					<input type="HIDDEN" id="ref" name="ref" value="{$ref}">
					<input type="HIDDEN" id="fsaction" name="fsaction" value="Order.ccResume">
				</form>
                <div class="clear"></div>

            </div> <!-- contentVisible -->

        </div> <!-- contentScrollCart -->
{if ($displayInline == false) || (! isset($displayInline))}
    </body>
</html>
{/if}