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

            // laoding image
            var gAlerts = 0;

            function setSystemLanguage()
            {
                changeSystemLanguage("{/literal}{$changelanguageinitfsaction}{literal}", "submitform", 'post');
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

    {/literal}

    {if $resetpasswordenabled}

        {literal}

            function forgotPassword()
            {
                document.getElementById("login").value = document.getElementById('login2Large').value;
                document.getElementById("fsaction").value = "Welcome.initForgotPassword";
                document.getElementById("submitform").submit();
                return false;
            }

        {/literal}

    {/if} {* end {if $resetpasswordenabled} *}

    {literal}

            /* only if password reset is enabled */
            function createAccount()
            {
                document.getElementById("fsaction").value = "Welcome.initNewAccount";
                document.getElementById("submitform").submit();
            }

            function validateLoginLargeScreen(e)
            {
                // Prevent the Sign in input button from triggering the input of the wrong form.
                e.preventDefault();

                var message = "{/literal}{#str_TitleError#}{literal}";
                gAlerts = 0;
                if (document.getElementById('login2Large').value.length == 0)
                {
                    message += "\n" + "{/literal}{#str_ErrorNoUserName#}{literal}";
                    highlight("login2Large");
                }

                if (document.getElementById('password2Large').value.length == 0)
                {
                    message += "\n" + "{/literal}{#str_ErrorNoPassword#}{literal}";
                    highlight("password2Large");
                }

                if (gAlerts > 0)
                {
                    alert(message);
                    return false;
                }
                else
                {
                    var format = ((document.location.protocol != 'https:') ? 1 : 0);

                    /* copy the values into the form we will submit and then submit it to the server */
                    document.getElementById("login").value = document.getElementById('login2Large').value;
                    document.getElementById("password").value = ((format == 0) ? document.getElementById('password2Large').value : hex_md5(document.getElementById('password2Large').value));
                    document.getElementById("fsaction").value = '{/literal}{$loginfsaction}{literal}';
                    document.getElementById("format").value = format;
                    document.getElementById("submitform").submit();

                    return false;
                }
            }

            window.onload = function()
            {
                initializeLargeScreenVersion();

                /* set a cookie to store the local time */
                var theDate = new Date();
                createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

                 /* Create the device type string and then set the cookie */
                var devString = makeDevCookie();
                createCookie("mawdd", devString, 90000);

                // Add listener to langauge select.
                var langSelectElement = document.getElementById('systemlanguagelist');
                if (langSelectElement)
                {
                    langSelectElement.addEventListener('change', function() {
                        return setSystemLanguage();
                    });
                }

    {/literal}{if $resetpasswordenabled}{literal}
                // Add listener to the forgot password link. 
                var forgotPasswordElement = document.getElementById('forgotPasswordLink');
                if (forgotPasswordElement)
                {
                    forgotPasswordElement.addEventListener('click', function() {
                        return forgotPassword();
                    });
                }
    {/literal}{/if}{literal}

                // Add listener to the sign in button
                var loginButtonElement = document.getElementById('loginButtonContainer');
                if (loginButtonElement)
                {
                    loginButtonElement.addEventListener('click', function(pEvent) {
                        validateLoginLargeScreen(pEvent); 
                        return false;
                    });
                }

                // Add listener to create account button.
                var createAccButton = document.getElementById('backButton');
                if (createAccButton)
                {
                    createAccButton.addEventListener('click', function() {
                        createAccount();
                    });
                }

				var togglePassword2LargeElement = document.getElementById('togglepassword2large');
                if (togglePassword2LargeElement)
                {
					togglePassword2LargeElement.addEventListener('click', function() {
                        togglePasswordVisibility(togglePassword2LargeElement, 'password2Large');
                    });
                }
            }

            function initializeLargeScreenVersion()
            {
                if ("{/literal}{$error}{literal}".length > 0 || "{/literal}{$info}{literal}".length)
                {
                    document.getElementById('message').style.display = 'block';
                }

            {/literal}

            {if $login2Template!= ''}

                {literal}

                // exception for IE7
                var browserValid = detectionIEBrowser(8);
                var paddindTop = 0;
                if (browserValid == false)
                {
                    paddindTop = parseInt(getStyle('contentTextNewAccount', 'paddingTop'));
                }
                var objDivLogin = document.getElementById('contentFormLogin');
                var objDivAccount = document.getElementById('contentTextNewAccount');
                var heightLogin = objDivLogin.offsetHeight;
                var heightAccount = objDivAccount.offsetHeight;
                if( heightAccount > heightLogin)
                {
                    objDivLogin.style.height = heightAccount + 'px';
                    objDivAccount.style.height = (heightAccount - paddindTop) + 'px';
                }
                else
                {
                    objDivLogin.style.height = heightLogin + 'px';
                    objDivAccount.style.height = (heightLogin - paddindTop) + 'px';
                }

                {/literal}

            {/if}

            {literal}

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
                                <div class="messageNoScriptLarge">
                                    {#str_ErrorNoJavaScript#}
                                </div>
                            </noscript>
                            <div id="returningCustomersHolder" {if $login2Template == ''}class="align-center"{/if}>
                                <div class="contentLogin">
                                    <form method="post" action="#">
                                        <div id="contentFormLogin">
                                            <div class="{$messageareaclass}" id="message">
                                                <p>{$error}{$info}</p>
                                            </div>
                                            <div class="top_gap_login">
                                                <label for="login2Large">{#str_LabelEmailorUsername#}:</label>
                                                <img src="{$brandroot}/images/asterisk.png" alt="*" />
                                                <input type="text" id="login2Large" name="login2Large" value="{$loginVal}" class="middle" />
                                                <img class="error_form_image" id="login2LargeCompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
                                                <div class="clear"></div>
                                            </div>
                                            <div class="top_gap_login">
                                                <label for="password2Large">{#str_LabelPassword#}:</label>
                                                <img src="{$brandroot}/images/asterisk.png" alt="*" />
                                                <div class="password-input-wrap">
                                                    <div class="password-background">
                                                        <input type="password" id="password2Large" name="password2Large" value="" class="middle" />
                                                        <button type="button" id="togglepassword2large" class="password-visibility password-show"></button>
                                                    </div>
                                                </div>
                                                <img class="error_form_image" id="password2LargeCompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
                                                <div class="clear"></div>
                                            </div>
                                            <div class="">
                                                {if $resetpasswordenabled}
                                                    <div class="forgotPasswordLink">
                                                        <a href="#" id="forgotPasswordLink">
                                                            {#str_LabelForgotPassword#}
                                                        </a>
                                                    </div>
                                                {/if}
                                                <div class="note">
                                                    <img src="{$brandroot}/images/asterisk.png" alt="*" />{#str_LabelCompulsoryFields#}
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btnRight btn-sign-in">
                                            <div class="contentBtn" id="loginButtonContainer">
                                                <div class="btn-green-left" ></div>
                                                <input type="submit" value="{#str_ButtonSignIn#}" class="btn-submit-green-middle" name="loginButton" id="loginButton"/>
                                                <div class="btn-accept-right"></div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </form>
                                </div>
                            </div>
                            {if $login2Template != ''}
                                {include file="$login2Template"}
                            {/if}
                            <div class="clear"></div>
                        </div>
                    </div>
                    {if $sidebaradditionalinfo != ''}
                        {include file="$sidebaradditionalinfo"}
                    {/if}
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <form id="submitform" name="submitform" method="post" action="" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />

            {if $ishighlevel == 1}
            	<input type="hidden" id="groupcode" name="groupcode" value="{$groupcode}" />
            {/if}

            <input type="hidden" id="fsaction" name="fsaction" value="" autocomplete="off"/>
            <input type="hidden" id="login" name="login" autocomplete="off"/>
            <input type="hidden" id="password" name="password" autocomplete="off"/>
            <input type="hidden" id="format" name="format" autocomplete="off"/>
            <input type="hidden" id="prtz" name="prtz" value="{$prtz}"/>
            <input type="hidden" id="mawebhluid" name="mawebhluid" value="{$mawebhluid}"/>
            <input type="hidden" id="mawebhlbr" name="mawebhlbr" value="{$mawebhlbr}"/>
            <input type="hidden" id="fhlbu" name="fhlbu" value="{$fhlbu}"/>
            <input type="hidden" id="ishighlevel" name="ishighlevel" value="{$ishighlevel}" />
            <input type="hidden" id="registerfsaction" name="registerfsaction" value="{$registerfsaction}" />
            <input type="hidden" id="fromregisterlink" name="fromregisterlink" value="{$fromregisterlink}" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>