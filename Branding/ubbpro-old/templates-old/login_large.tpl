<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelWelcome#}</title>

        {include file="includes/customerinclude_large.tpl"}

        <script type="text/javascript">
            //<![CDATA[

    {literal}

            // laoding image
            var gAlerts = 0;

            function setSystemLanguage()
            {
                changeSystemLanguage("Welcome.initialize");
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

            function validateLoginLargeScreen()
            {
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
                    var format = ((document.location.protocol != 'https:') ? 0 : 0);

                    /* copy the values into the form we will submit and then submit it to the server */
                    document.getElementById("login").value = document.getElementById('login2Large').value;
                    document.getElementById("password").value = ((format == 0) ? document.getElementById('password2Large').value : hex_md5(document.getElementById('password2Large').value));
                    document.getElementById("fsaction").value = "Welcome.processLogin";
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
                        <div class="content">
                            <noscript>
                                <div class="messageNoScriptLarge">
                                    {#str_ErrorNoJavaScript#}
                                </div>
                            </noscript>
                            <div id="returningCustomersHolder" {if $login2Template == ''}class="align-center"{/if}>
                                <div class="contentLogin">
                                    <form method="post" action="#" onsubmit="return false;">
                                        <div id="contentFormLogin">
                                            <div class="message {if $info != ''}confirmation{/if}" id="message">
                                                {$error}{$info}
                                            </div>
                                            <div class="top_gap_login">
                                                <label for="login2Large">{#str_LabelUserName#}:</label>
                                                <img src="{$webroot}/images/asterisk.png" alt="*" />
                                                <input type="text" id="login2Large" name="login2Large" value='{$loginVal}' class="middle" />
                                                <img class="error_form_image" id="login2LargeCompulsory" src="{$webroot}/images/asterisk.png" alt="*" />
                                                <div class="clear"></div>
                                            </div>
                                            <div class="top_gap_login">
                                                <label for="password2Large">{#str_LabelPassword#}:</label>
                                                <img src="{$webroot}/images/asterisk.png" alt="*" />
                                                <input type="password" id="password2Large" name="password2Large" value="" class="middle" />
                                                <img class="error_form_image" id="password2LargeCompulsory" src="{$webroot}/images/asterisk.png" alt="*" />
                                                <div class="clear"></div>
                                            </div>
                                            <div class="">
                                                {if $resetpasswordenabled}
                                                    <div class="forgotPasswordLink">
                                                        <a href="#" id="forgotPasswordLink" onclick="return forgotPassword();">
                                                            {#str_LabelForgotPassword#}
                                                        </a>
                                                    </div>
                                                {/if}
                                                <div class="note">
                                                    <img src="{$webroot}/images/asterisk.png" alt="*" />{#str_LabelCompulsoryFields#}
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btnRight btn-sign-in">
                                            <div class="contentBtn" onclick="validateLoginLargeScreen(); return false;">
                                                <input type="submit" value="{#str_ButtonSignIn#}" class="btn-submit-green-middle" name="loginButton" id="loginButton"/>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </form>
                                </div>
                            </div>
                            {if $login2Template!= ''}
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
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="login" name="login" />
            <input type="hidden" id="password" name="password" />
            <input type="hidden" id="format" name="format" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
        </form>
    </body>
</html>