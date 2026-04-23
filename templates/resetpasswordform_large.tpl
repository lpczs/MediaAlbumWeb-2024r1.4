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
			var passwordStrength = new TPXPasswordStrength({
				minStrength: {/literal}{$passwordstrengthmin}{literal},
				weakString: "{/literal}{#str_ErrorPasswordTooWeak#}{literal}",
				strengthStrings: {
					0: "{/literal}{#str_LabelStartTyping#}{literal}",
					1: "{/literal}{#str_LabelPasswordVeryWeak#}{literal}",
					2: "{/literal}{#str_LabelPasswordWeak#}{literal}",
					3: "{/literal}{#str_LabelPasswordMedium#}{literal}",
					4: "{/literal}{#str_LabelPasswordStrong#}{literal}",
					5: "{/literal}{#str_LabelPasswordVeryStrong#}{literal}"
				}
			});

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
                // Prevent the Sign in input button from triggering the input of the wrong form.
                e.preventDefault();

				gAlerts = 0;
				var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";

				var newpassword = document.getElementById('newpassword');
                var passwordStrengthErrorText = passwordStrength.getErrorText();

				newpassword.className = newpassword.className.replace("errorInput", "");

				if (newpassword.value.length == 0)
				{
					message += "\n" + "{/literal}{#str_ErrorNoNewPassword#}{literal}";
					highlight("newpassword");
				}

				if (newpassword.value.length < 5)
				{
					message += "\n" + "{/literal}{#str_MessageCompulsoryPasswordLength#}{literal}";
					message = message.replace("^0", '5');
					highlight("newpassword");
				}

				if (passwordStrengthErrorText != '')
				{
					message += passwordStrengthErrorText;
					highlight("newpassword");
				}

				if (gAlerts > 0)
				{
					alert(message);
					return false;
				}

				var format = ((document.location.protocol != 'https:') ? 1 : 0);

				/* copy the values into the form we will submit and then submit it to the server */
				form = document.submitform;
				form.data2.value = ((format == 0) ? newpassword.value : hex_md5(newpassword.value));
				form.fsaction.value = "Welcome.resetPasswordProcess";
				document.getElementById("format").value = format;

				form.submit();

				return false;
			}

            window.onload = function()
            {
                if ("{/literal}{$error}{literal}".length > 0)
                {
                    document.getElementById('message').style.display = 'block';
                }

                // Add the listener to the button to submit the password reset.
                document.getElementById('submitResetFormButton').addEventListener('click', function() {
                    validateDataEntry(event);
                    return false;
                });

                // Add the listener to the button to submit the password reset.
                document.getElementById('newpassword').addEventListener('keyup', function() {
                    passwordStrength.scorePassword(this.value, 'strengthvalue', 'strengthtext');
                    return false;
                });

                // Add listener to langauge select.
                var systemlanguagelist = document.getElementById('systemlanguagelist');
                if(systemlanguagelist)
                {
                    document.getElementById('systemlanguagelist').addEventListener('change', function() {
                        return setSystemLanguage();
                    });
                }

				// Add listener to show/hide password.
				var toggleNewPasswordElement = document.getElementById('togglenewpassword');
                if (toggleNewPasswordElement)
                {
					toggleNewPasswordElement.addEventListener('click', function() {
                        togglePasswordVisibility(toggleNewPasswordElement, 'newpassword');
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
                        <div class="content log-in-wrap reset-password">
                            <div id="resetPasswordHolder" {if $login2Template == ''}class="align-center"{/if}>
                                <form id="formReset" method="post" action="#">
                                    <div class="contentForgot">
                                        <div class="message" id="message">
                                            {$error}
                                        </div>
                                         <div class="top_gap">
                                            <label for="newpassword">{#str_LabelNewPassword#}: </label>
                                            <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <div class="password-input-wrap">
                                                <div class="password-background">
                                                    <input type="password" id="newpassword" name="newpassword" value="" class="middle" />
                                                    <button type="button" id="togglenewpassword" class="password-visibility password-show"></button>
                                                </div>
                                                <div class="progress-wrap">
                                                    <progress id="strengthvalue" value="0" min="0" max="5"></progress>
                                                    <p>{#str_LabelPasswordStrength#}: <span id="strengthtext">{#str_LabelStartTyping#}</span></p>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                    <div class="note">
                                        <img src="{$brandroot}/images/asterisk.png" alt="*" />{#str_LabelCompulsoryFields#}
                                    </div>
                                    <div class="buttonBottomInside buttonForgot">
                                        <div class="btnRight">
                                            <div class="contentBtn" id="submitResetFormButton">
                                                <div class="btn-green-left" ></div>
                                                <input type="submit" value="{#str_ButtonSaveNewPassword#}" class="btn-submit-green-middle" name="resetButton" id="resetButton" />
                                                <div class="btn-accept-right"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </form>
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
        <form id="submitform" name="submitform" method="post" action="" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="data2" name="data2" />
            <input type="hidden" id="data3" name="data3" value="{$requesttoken}" />
            <input type="hidden" id="format" name="format" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>