<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelWelcome#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="includes/customerinclude_large.tpl"}

        <script type="text/javascript" {$nonce}>
                //<![CDATA[
{literal}

        function setSystemLanguage()
        {
            changeSystemLanguage("Share.preview", "submitformlogin", 'post');
        }


        function validateLoginLargeScreen(pEvent)
        {
            // Prevent the Sign in input button from triggering the input of the form.
            pEvent.preventDefault();

            var message = "{/literal}{#str_TitleError#}{literal}";
            gAlerts = 0;
            if (document.getElementById('password2').value.length == 0)
            {
                message += "\n" + "{/literal}{#str_ErrorNoPassword#}{literal}";
                highlight("password2");
            }

            if (gAlerts > 0)
            {
                alert(message);
                return false;
            }
            else
            {
                /* copy the values into the form we will submit and then submit it to the server */
				var format = ((document.location.protocol != 'https:') ? 1 : 0);
                document.getElementById("password").value = ((format == 1) ? hex_md5(document.getElementById('password2').value) : document.getElementById('password2').value);
				document.getElementById("format").value = format;
                document.getElementById("submitformlogin").submit();
                return false;
            }

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

        window.onload = function()
        {
            /* set a cookie to store the local time */
            var theDate = new Date();
            createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

            if ("{/literal}{$error}{literal}".length > 0)
            {
                document.getElementById('message').style.display = 'block';
            }

            var passwordInput = document.getElementById('password2');
            if (passwordInput)
            {
                // Add listener to the password input for the enter key.
                passwordInput.addEventListener('keyup', function(pEvent) {
                    if (passwordInput.value != '')
                    {
                        // Check for enter key. 
                        if (enterKeyPressed(pEvent))
                        {
                            return validateLoginLargeScreen(pEvent);
                        }
                    }

                    return false;
                });
            }

            // Add listener to continue button.
            var continueButton = document.getElementById('loginButton');
            if (continueButton)
            {
                continueButton.addEventListener('click', function(pEvent) {
                    return validateLoginLargeScreen(pEvent);
                });
            }

            // Add listener to langauge select.
            var systemLanguageElement = document.getElementById('systemlanguagelist');
            if (systemLanguageElement)
            {
                systemLanguageElement.addEventListener('change', function() {
                    setSystemLanguage();
                });           
            }

			// Add listener to show/hide password.
			var togglePassword2Element = document.getElementById('togglepassword2');
			if (togglePassword2Element)
			{
				togglePassword2Element.addEventListener('click', function() {
					togglePasswordVisibility(togglePassword2Element, 'password2');
				});
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
                        <h2 class="title-bar">
                            {#str_TitleSignIn#}
                        </h2>
                        <div class="content log-in-wrap">
                            <noscript>
                                <div class="messageNoScript">
                                    {#str_ErrorNoJavaScript#}
                                </div>
                            </noscript>
                            <div id="returningCustomersHolder" {if $login2Template == ''}class="align-center"{/if}>
                                <h2 class="title-login">
                                    {#str_previewProtectPassword#}
                                </h2>
                                <form method="post" action="#">
                                    <div class="message" id="message">
                                        {$error}
                                    </div>
                                    <div class="top_gap_login">
                                        <label for="password2">{#str_LabelPassword#}:</label>
                                        <img src="{$brandroot}/images/asterisk.png" alt="*" />
                                        <div class="password-input-wrap">
                                            <div class="password-background">
                                                <input type="password" id="password2" name="password2" value="" class="middle" />
                                                <button type="button" id="togglepassword2" class="password-visibility password-show"></button>
                                            </div>
                                        </div>
                                        <img id="passwordcompulsory" src="{$webroot}/images/asterisk.png" alt="*" class="error_form_image"/>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="indent-top btnRight">
                                        <div class="contentBtn" id="loginButton">
                                            <div class="btn-green-left" ></div>
                                            <input type="submit" value="{#str_ButtonContinue#}" class="btn-submit-green-middle" />
                                            <div class="btn-accept-right"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </form>
                            </div>
                        </div>
                    </div>

{if $sidebaradditionalinfo != ''}

                    {include file="$sidebaradditionalinfo"}
                    `
{/if}

                </div>
            </div>
        </div>

        <form id="submitformlogin" name="submitformlogin" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="ref2" name="ref2" value="{$ref2}" />
            <input type="hidden" id="orderitemid" name="orderitemid" value="{$orderitemid}" />
            <input type="hidden" id="source" name="source" value="{$source}" />
            <input type="hidden" id="webbrandcode" name="webbrandcode" value="{$webbrandcode}" />
            <input type="hidden" id="fsaction" name="fsaction" value="Share.login" />
            <input type="hidden" id="password" name="password" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
			<input type="hidden" id="format" name="format" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>
