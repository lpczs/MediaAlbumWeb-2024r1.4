<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelWelcome#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="includes/customerinclude_large.tpl"}
        {include file="order/finalstageincludedecorator.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[
{literal}

        function setSystemLanguage()
        {
            changeSystemLanguage("Share.reorder", "submitform", 'post');
        }

        window.onload = function()
        {
            /* set a cookie to store the local time */
            var theDate = new Date();
            createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

            if ("{/literal}{$error}{literal}".length > 0)
            {
                document.getElementById('message').style.display = 'block';
            }
        }

{/literal}
        //]]>
        </script>

    </head>
    <body>

        <div class="outer-page{if $sidebarleft != ''} fullsize-outer-page{/if}">
            <div id="header" class="headertop">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>
            {if $sidebarleft != ''}
                {include file="$sidebarleft"}
            {/if}
            <div id="contentHolder">
                <div id="pageFooterHolder" class="fullsizepage">
                    <div id="page" class="section">
                        <h1 class="title-bar">
                            {#str_LabelItemDoesNotExist#}
                        </h1>

                        <noscript>
                            <div class="messageNoScriptLarge">
                                {#str_ErrorNoJavaScript#}
                            </div>
                        </noscript>

                        <div class="content-footer">
                            {$result}
                        </div>
                        {if $mainwebsiteurl != ''}
                            <div class="contentDottedImage"></div>
                            <div class="btnRightContinue">
                                <div class="contentBtn" data-decorator="fnRedirect" data-url="{$mainwebsiteurl|escape}">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle">{#str_ButtonContinue#}</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="action" name="action" value="{$action}" />
            <input type="hidden" id="orderitemid" name="orderitemid" value="{$orderitemid}" />
            <input type="hidden" id="fsaction" name="fsaction" value="Share.preview" />
        </form>

    </body>
</html>
