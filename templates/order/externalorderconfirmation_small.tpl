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
        {include file="order/finalstageincludedecorator.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

            {include file="order/orderconfirmation.tpl"}

            //]]>
        </script>

    </head>
    <body>

        <div id="headerSmall" class="header">
            <div class="headerinside">
				<div class="headerLeft">
                    {if $mainwebsiteurl == ""}
                        <img src="{$headerlogoasset}" alt=""/>
                    {else}
                        <a href="{$mainwebsiteurl}" border="0">
                            <img src="{$headerlogoasset}" alt=""/>
                        </a>
                    {/if}
                </div>

				<div class="headerRight">

					<div class="headerSeparator separatorLeft"></div>
					<div class="headerSeparator separatorRight"></div>

					{if $systemlanguagelist|@count_characters > 0}

					<div class="headerSeparator separatorLeft"></div>
					<div class="headerSeparator separatorRight"></div>

					<div class="languageSection" data-decorator="toggleLanguageOption">
						<img src="../images/icons/language_icon_v2.png" alt="" class="imgLanguage" />
						<div class="languageImgPopup" id="img-language-popup"></div>
					</div> <!-- languageSection -->

					{/if}


					<div class="clear"></div>

				</div> <!-- headerRight -->

				<div class="clear"></div>
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

                    {else}

                        <div class="orderInformationMessage">
                            {#str_MessageOrderConfirmation3#}
                        </div>

                    {/if} {* end {if $cciCompletionMessage != ""} *}

                </div>

                {if $mainwebsiteurl != ''}

                <div class="buttonBottomSection">
					<a href="{$mainwebsiteurl}">
						<div data-decorator="fnRedirect" data-url="{$mainwebsiteurl|escape}">
							<div class="btnAction btnContinue">
								<div class="btnContinueContent">{#str_ButtonContinue#}</div>
							</div>
						</div>
					</a>
                </div>
                <div class="clear"></div>

                {/if}{* end {if $mainwebsiteurl != ''} *}

            </div> <!-- contentVisible -->

        </div> <!-- contentScrollCart -->

{if $isajaxcall == false}

    </body>

</html>

{/if}