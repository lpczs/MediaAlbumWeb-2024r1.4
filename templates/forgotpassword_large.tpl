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
            function setSystemLanguage()
            {
                changeSystemLanguage("Welcome.initForgotPassword", "submitform", 'post');
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
                var theForm = document.getElementById('formForgot');
                if (theForm.login2.value.length == 0)
                {
                    message += "\n" + "{/literal}{#str_ErrorNoUserName#}{literal}";
                    highlight("login2");
                    gAlerts = 1;
                }

                if (gAlerts > 0)
                {
                    alert(message);
                }
                else
                {
                    /* copy the values into the form we will submit and then submit it to the server */
                    document.getElementById("login").value = theForm.login2.value;
                    document.getElementById("fsaction").value = "Welcome.resetPasswordRequest";
                    document.getElementById("format").value = ((document.location.protocol != 'https:') ? 1 : 0);
                    document.getElementById("submitform").submit();
                }
            }

            function cancelDataEntry()
            {
                document.getElementById("login").value = document.getElementById('login2').value;
				document.getElementById("fsaction").value = "{/literal}{$cancelfsaction}{literal}";
                document.getElementById("submitform").submit();
                return false;
            }

            window.onload = function()
            {
                if (("{/literal}{$error}{literal}".length > 0) || ("{/literal}{$info}{literal}".length > 0))
                {
                    document.getElementById('message').style.display = 'block';
                }

                // Add listener to the sign in button.
                document.getElementById('resetPasswordButton').addEventListener('click', function(pEvent) {
                    validateDataEntry(pEvent); 
                    return false;
                });

                // Add listener to the back button.
                var backButtonElement = document.getElementById('backButton');
                if (backButtonElement)
                {
                    backButtonElement.addEventListener('click', function() {
                        cancelDataEntry();
                        return false;
                    });
                }

                // Add listener to langauge select.
                document.getElementById('systemlanguagelist').addEventListener('change', function() {
                    return setSystemLanguage();
                });
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
                                <p>
                                    {#str_TextResetPassword#}
                                </p>
                                <form id="formForgot" method="post" action="#">
                                    <div class="contentForgot">

                                        <div class="{$messageareaclass}" id="message">
                                            <span>{$error}{$info}</span>
                                        </div>

                                        <div class="top_gap">
                                            <label for="login2">{#str_LabelEmailorUsername#}:</label>
                                            <img class="valign-center" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="login2" name="login2" value='{$loginval}' class="middle" />
                                            <img id="login2compulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*" />
                                            <div class="clear"></div>
                                        </div>

                                        <div class="note">
                                            <img src="{$brandroot}/images/asterisk.png" alt="*" />{#str_LabelCompulsoryFields#}
                                        </div>

                                    </div>

                                    <div class="buttonBottomInside buttonForgot">
                                        {if $showbackbutton}
                                        <div class="btnLeft">
                                            <div class="contentBtn" id="backButton">
                                                <div class="btn-blue-arrow-left" ></div>
                                                <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                                <div class="btn-blue-right"></div>
                                            </div>
                                        </div>
                                        {/if}
                                        <div class="btnRight">
                                            <div class="contentBtn" id="resetPasswordButton">
                                                <div class="btn-green-left" ></div>
                                                <input type="submit" value="{#str_ButtonSendResetLink#}" class="btn-submit-green-middle" name="resetButton" id="resetButton" />
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
        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
            <input type="hidden" id="login" name="login" />
			<input type="hidden" id="prtz" name="prtz" value="{$prtz}"/>
            <input type="hidden" id="mawebhluid" name="mawebhluid" value="{$mawebhluid}"/>
            <input type="hidden" id="mawebhlbr" name="mawebhlbr" value="{$mawebhlbr}"/>
            <input type="hidden" id="fhlbu" name="fhlbu" value="{$fhlbu}"/>
            <input type="hidden" id="ishighlevel" name="ishighlevel" value="{$ishighlevel}" />
            <input type="hidden" id="format" name="format" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />

			{if $ishighlevel == 1}
            	<input type="hidden" id="groupcode" name="groupcode" value="{$groupcode}" />
				<input type="hidden" id="fromregisterlink" name="fromregisterlink" value="{$fromregisterlink}" />
            {/if}
            <input type="hidden" id="passwordlinkexpired" name="passwordlinkexpired" value="{$passwordlinkexpired}" />
            <input type="hidden" id="passwordresetrequesttoken" name="passwordresetrequesttoken" value="{$passwordresetrequesttoken}" />
			<input type="hidden" id="passwordresetdatabasetoken" name="passwordresetdatabasetoken" value="{$passwordresetdatabasetoken}" />
        </form>
    </body>
</html>