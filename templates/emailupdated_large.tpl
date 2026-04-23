<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleEmailUpdated#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        {include file="includes/customerinclude_large.tpl"}
        <script type="text/javascript" {$nonce}>
            //<![CDATA[
            {literal}

            function setSystemLanguage()
            {
                changeSystemLanguage("Welcome.resetPassword", "submitform", 'post');
            }


            window.onload = function()
            {
                document.getElementById('closeButton').onclick = function(){
                    document.getElementById("submitform").submit();
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
                <div id="pageFooterHolder" {if $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                    <div id="page" class="section">
                        <h1 class="title-bar">
                            {#str_TitleEmailUpdated#}
                        </h1>
                        <noscript>
                            <div class="messageNoScript">
                                {#str_ErrorNoJavaScript#}
                            </div>
                        </noscript>
                        <div class="content log-in-wrap message-wrap">
                            <p>{$emailUpdatedMessage}</[p]>
                            
                            <div class="contentBtn" id="closeButton">
                                <div class="btn-blue-left"></div>
                                <div class="btn-blue-middle">{#str_ButtonSignBackIn#}</div>
                                <div class="btn-blue-right"></div>
                            </div>
                        </div>
                    </div>
                    {if $sidebaradditionalinfo != ''}
                        {include file="$sidebaradditionalinfo"}
                    {/if}

                    {if $sidebarcontactdetails != ''}
                        <div class="side-outer-panel">
                            {include file="$sidebarcontactdetails"}
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="fsaction" name="fsaction" value="{$action}" />
        </form>
    </body>
</html>