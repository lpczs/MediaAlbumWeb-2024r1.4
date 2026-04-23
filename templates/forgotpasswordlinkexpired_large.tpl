<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelForgotPassword#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        {include file="includes/customerinclude_large.tpl"}
        <script type="text/javascript" {$nonce}>
            {literal}
			function forgotPassword()
			{
				document.getElementById("fsaction").value = "Welcome.initForgotPassword";
				document.getElementById("submitform").submit();
				return false;
			}

			function setSystemLanguage()
			{
				changeSystemLanguage("Welcome.resetPassword", "submitform", "post");
			}

            window.onload = function()
            {
                // Add listener to the forgotten password link. 
                document.getElementById('forgotPasswordLink').addEventListener('click', function() {
                    return forgotPassword();
                });

                // Add listener to langauge select.
                document.getElementById('systemlanguagelist').addEventListener('change', function() {
                    return setSystemLanguage();
                });
            };
            {/literal}
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
                            {#str_LabelForgottenPassword#}
                        </h1>
                        <div class="content log-in-wrap reset-password">
                            <p>
                                {#str_MessageResetPasswordLinkExpired#}
                            </p>
                            <p>
                                {#str_MessageResetPasswordLinkExpiredGoBack#}
                                <a href="#" id="forgotPasswordLink">{#str_LabelForgottenPassword#}</a>
                            </p>

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
        <form id="submitform" name="submitform" method="post" action="" accept-charset="utf-8">
            <input type="hidden" id="fsaction" name="fsaction" value="" autocomplete="off"/>
            <input type="hidden" id="passwordlinkexpired" name="passwordlinkexpired" value="true" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>