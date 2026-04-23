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
            //<![CDATA[
            {literal}
            function forgotPassword()
			{
				document.getElementById("fsaction").value = "Welcome.initForgotPassword";
				document.getElementById("submitform").submit();
				return false;
			}

            function setSystemLanguage()
            {
                changeSystemLanguage("Welcome.resetPassword", "submitform", 'post');
            }

            function highlight(field)
            {
                var inputObj = document.getElementById(field);
                if (inputObj)
                {
                    inputObj.className = inputObj.className + ' errorInput';
                    gAlerts = 1;
                }
            }

			function validateDataEntry(e)
			{
				gAlerts = 0;
				var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";

				var authcode = document.getElementById('authcode');

				authcode.className = authcode.className.replace("errorInput", "");

				if (authcode.value.length == 0)
                {
                    message += "\n" + "{/literal}{#str_LabelEnterAuthenticationCode#}{literal}";
                    highlight("authcode");
                    gAlerts = 1;
                }

				if (gAlerts > 0)
				{
					alert(message);
					return false;
				}

				/* copy the values into the form we will submit and then submit it to the server */
				form = document.submitform;
				form.data1.value = authcode.value;
				form.fsaction.value = "Welcome.resetPasswordProcessAuthCode";

				form.submit();

				return false;
			}

            window.onload = function()
            {
                if ("{/literal}{$error}{literal}".length > 0)
                {
                    document.getElementById('message').style.display = 'block';
                }

                // Add the listener to the button to submit the auth code.
                document.getElementById('submitAuthCodeButton').addEventListener('click', function() {
                    validateDataEntry(event);
                    return false;
                });

                // Add listener to the lost your auth code link. 
                document.getElementById('forgotPasswordLink').addEventListener('click', function() {
                    return forgotPassword();
                });

                // Add listener to langauge select.
                var systemlanguagelist = document.getElementById('systemlanguagelist');
                if(systemlanguagelist)
                {
                    document.getElementById('systemlanguagelist').addEventListener('change', function() {
                        return setSystemLanguage();
                    });
                }
            };
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
                            {#str_LabelForgotPassword#}
                        </h1>
                        <noscript>
                            <div class="messageNoScript">
                                {#str_ErrorNoJavaScript#}
                            </div>
                        </noscript>
                        <div class="content log-in-wrap reset-password authCode">
                            <div id="resetPasswordHolder" {if $login2Template == ''}class="align-center"{/if}>
                                <form id="formReset" method="post" action="#">
                                    <div class="contentForgot">
                                        <div class="message error" id="message">
                                            {$error}
                                        </div>
                                        <div class="top_gap">
                                            <label for="authcode">{#str_LabelEnterAuthenticationCode#}:</label>
                                            <img class="valign-center" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="authcode" name="authcode" class="middle"/>
                                            <img id="authcodecompulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*" />
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                    <div class="note">
                                        <img src="{$brandroot}/images/asterisk.png" alt="*" />{#str_LabelCompulsoryFields#}
                                    </div>
                                    <div class="buttonBottomInside buttonForgot">
                                        <div class="btnRight">
                                            <div class="contentBtn" id="submitAuthCodeButton">
                                                <div class="btn-green-left" ></div>
                                                <input type="button" value="{#str_ButtonNext#}" class="btn-submit-green-middle" name="resetButton" id="resetButton" />
                                                <div class="btn-green-arrow-right"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </form>
                                <p class="note">
                                    {#str_LabelLostYourAuthCode#}
                                    <a href="#" id="forgotPasswordLink">{#str_LabelStartAgain#}</a>
                                </p>
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
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="data1" name="data1" />
            <input type="hidden" id="data2" name="data2" value="{$requesttoken}" />
            <input type="hidden" id="passwordlinkexpired" name="passwordlinkexpired" value="true" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>