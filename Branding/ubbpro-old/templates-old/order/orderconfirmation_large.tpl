<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleOrderConfirmation#}</title>
        {include file="includes/customerinclude_large.tpl"}

        <script type="text/javascript">
            //<![CDATA[

            {include file="order/orderconfirmation.tpl"}

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
            <div class="contentNavigation {if $sidebarleft != ''} fullsize-navigation{/if}">
                <div class="contentNavigationImage">
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationBloc">
                        <div class="navigationActiveLeft"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="contentNavigationText">
                    <div class="labelNavigation">{#str_LabelNavigationCart#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationShippingBilling#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationPayment#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationConfirmation#}</div>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="contentScroll" class="contentScrollCart">
                {if $sidebarleft != ''}
                    {include file="$sidebarleft"}
                {/if}
                <div id="contentHolder">
                    <div id="pageFooterHolder" class="fullsizepage">
                        <div id="page" class="section">
                            <div class="title-bar">
                                <h2 class="title-confirmation">
                                    {#str_TitleOrderConfirmation#}
                                </h2>
                            </div>
                            <div class="content-footer">
                                <div class="confirmationBoldText">
                                    {#str_MessageOrderConfirmation2#}
                                </div>
                                <div class="confirmationMessage">
                                    {$str_MessageOrderConfirmation1}
                                </div>
                                <div class="confirmationMessage">
                                    {#str_MessageOrderConfirmation3#}
                                </div>
                                {if $cciCompletionMessage != ""}
                                    <div class="confirmationMessage">s
                                        {$cciCompletionMessage}
                                    </div>
                                {/if}
                            </div>
                            {if $mainwebsiteurl != ''}
                                <div class="contentDottedImage"></div>
                                <div class="btnRightContinue">
                                    <div class="contentBtn" onclick="window.location.replace('{$mainwebsiteurl|escape}');">
                                        <div class="btn-green-middle">{#str_ButtonContinue#}</div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
