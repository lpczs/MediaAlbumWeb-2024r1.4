<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta name="viewport" content = "width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelWelcome#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="includes/customerinclude_small.tpl"}

        <script type="text/javascript" {$nonce}>
                //<![CDATA[
{literal}

        var gDialogIsOpen = false;
        var gMaxWidth = 600;

        window.addEventListener('DOMContentLoaded', function(e) {
            document.body.addEventListener('click', decoratorListener);
        });

        // wrapper for changeSystemLanguageSmallScreen
        function fnChangeSystemLanguageSmallScreen(pElement)
        {
            if (!pElement) {
                return false;
            }

            return changeSystemLanguageSmallScreen(pElement.getAttribute('data-code'));
        }

        /**
        * changeSystemLanguageSmallScreen
        *
        * Set the new language and reload the page
        */
        function changeSystemLanguageSmallScreen(pLanguageCode)
        {
            createCookie("maweblocale", pLanguageCode, 24 * 365);
            document.submitform.fsaction.value = "Share.preview";
            document.submitform.submit();
        }

        /**
        * toggleLanguageOption
        *
        * Open or close the langauge list and apply the correct syle compare to the status
        */
        function toggleLanguageOption()
        {
            if (gDialogIsOpen == false)
            {
                document.getElementById('language-list-popup').style.display = 'block';
                document.getElementById('img-language-popup').style.backgroundImage="url({/literal}{$webroot}{literal}/images/icons/toggle_up_grey_v2.png)";
                gDialogIsOpen = true;
            }
            else
            {
                document.getElementById('language-list-popup').style.display = 'none';
                document.getElementById('img-language-popup').style.backgroundImage="url({/literal}{$webroot}{literal}/images/icons/toggle_down_grey_v2.png)";
                gDialogIsOpen = false;
            }
        }

        window.onload = function()
        {
            /* set a cookie to store the local time */
            var theDate = new Date();
            createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);
            initializeSmallScreenVersion(true);

			// Add listener to show/hide password.
			var togglePassword2small2Element = document.getElementById('togglepassword2small');
			if (togglePassword2small2Element)
			{
				togglePassword2small2Element.addEventListener('click', function() {
					togglePasswordVisibility(togglePassword2small2Element, 'password2small');
				});
			}
        }

        window.reisze = function()
        {
            resizeApp();
        }

        function resizeApplication()
        {
            // reset the scroll position to force the page to be at the top when displayed
            gScreenWidth = 0;

            initializeSmallScreenVersion(false);

            if (gDialogStatus == 'open')
            {
                setDialogPosition();
            }
        }

        function initializeSmallScreenVersion(pInit)
        {
            if (pInit)
            {
                //show loading dialog
                showLoadingDialog();
            }

            // main Bloc Size
            var width = document.body.offsetWidth;

            // store the screen size
            gScreenWidth = width;

            document.getElementById('contentBlocSite').style.width = width;

            var contentScrollCart = document.getElementById('contentScrollCart');
            var styleContentScrollCart = contentScrollCart.currentStyle || window.getComputedStyle(contentScrollCart);
            gContentScrollCart = parseIntStyle(styleContentScrollCart.paddingLeft) + parseIntStyle(styleContentScrollCart.paddingRight);

            var outerBox = document.getElementById('outerBox');
            var styleOuterBox = outerBox.currentStyle || window.getComputedStyle(outerBox);
            gOuterBox = parseIntStyle(styleOuterBox.paddingLeft) + parseIntStyle(styleOuterBox.paddingRight);
            gOuterBox += parseIntStyle(styleOuterBox.borderLeftWidth) + parseIntStyle(styleOuterBox.borderRightWidth);

            var outerBoxPadding = document.getElementById('outerBoxPadding');
            var styleOuterBoxPadding = outerBoxPadding.currentStyle || window.getComputedStyle(outerBoxPadding);
            gOuterBoxPadding = parseIntStyle(styleOuterBoxPadding.paddingLeft) + parseIntStyle(styleOuterBoxPadding.paddingRight);
            gOuterBoxPadding += parseIntStyle(styleOuterBoxPadding.marginLeft) + parseIntStyle(styleOuterBoxPadding.marginRight);

            if (gScreenWidth > gMaxWidth)
            {
                gOuterBoxContentBloc = gMaxWidth - gOuterBox - gOuterBoxPadding;
            }
            else
            {
                gOuterBoxContentBloc = gScreenWidth - gContentScrollCart - gOuterBox - gOuterBoxPadding;
            }

             // add scrollbar if needed
            setScrollAreaHeight('contentLeftScrollLogin', '');

            // if an error, display dialog

    {/literal}

        {if $error != ''}

            {literal}

            if (pInit == true)
            {

                createDialog("{/literal}{#str_TitleError#}{literal}", "{/literal}{$error}{literal}", function(e) {
                    closeDialog(e);
                });

            }

            {/literal}

        {/if}

    {literal}

            // close loading dialog
            closeLoadingDialog();
        }

        // wrapper function for validateLoginSmallScreen
        function fnValidateLoginSmallScreen(pElement)
        {
            return validateLoginSmallScreen();
        }

        function validateLoginSmallScreen()
        {
            var message = "";
            if (document.getElementById('password2small').value.length == 0)
            {
                message += "{/literal}{#str_ErrorNoPassword#}{literal}<br/>";
            }

            if (message != '')
            {
                createDialog("{/literal}{#str_TitleError#}{literal}", message, function(e) {
                    closeDialog(e);
                });
            }
            else
            {
                var submitForm = document.getElementById("submitform");

                /* copy the values into the form we will submit and then submit it to the server */
                submitForm.mobile.value = true;
				var format = ((document.location.protocol != 'https:') ? 1 : 0);
                submitForm.password.value = ((format == 1) ? hex_md5(document.getElementById('password2small').value) : document.getElementById('password2small').value);
				submitForm.format.value = format;
                submitForm.fsaction.value = "Share.login";
                submitForm.submit();
            }
        }

{/literal}
        //]]>
        </script>

    </head>
    <body>

        <!-- DIALOGS -->

        <div id="shim" class="shim">&nbsp;</div>
        <div id="shimSpinner" class="shimSpinner">&nbsp;</div>

        <div id="dialogOuter" class="dialogOuter"></div>

        <div id="dialogLoading" class="dialogLoading">
          <img class="loadingImage" src="{$webroot}/images/mobile_loading.png" alt=""/>
        </div>

        <!-- END DIALOGS -->

         <!-- HIDDEN DIV TO ACCESS STYLE -->

        <div id="contentScrollCart" class="contentScrollCart hide"></div>
        <div id="outerBox" class="outerBox hide"></div>
        <div id="outerBoxPadding" class="outerBoxPadding hide"></div>

        <!-- END HIDDEN DIV TO ACCESS STYLE -->

         <div id="outerPage" class="outerPage">

            <div id="headerSmall" class="header">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>

            <div class="tpxPopupOption" id="language-list-popup">
                <div class="tpx-popup-option-bloc">
                    <div class="tpx-popup-option-content">
                        {$systemlanguagelist}
                    </div>
                </div>
            </div>

            <div id="contentBlocSite" class="contentBlocSite">

                <div id="loadingGif" class="loadingGif"></div>

                <div id="contentLeftScrollLogin" class="contentScrollCart">

                    <div class="contentVisible">

                        <div class="pageLabel">

                            {#str_TitleSignIn#}

                        </div> <!-- pageLabel -->

                        <noscript>
                            <div class="messageNoScript">
                                {#str_ErrorNoJavaScript#}
                            </div>
                        </noscript>

                        <div id="contentHolder">

                            <div class="outerBox outerBoxPadding">

                                <div>
                                    <div class="formLine1">
                                        <label for="password2small">{#str_LabelPassword#}:</label>
                                    </div>

                                    <div class="formLine2 password-input-wrap">
                                        <input type="password" id="password2small" name="password2small" value="" class="middle" />
										<button type="button" id="togglepassword2small" class="password-visibility password-show"></button>
                                    </div>

                                    <div class="clear"></div>
                                </div>

                                <div class="signInBtn">

                                    <div class="btnAction btnContinue" data-decorator="fnValidateLoginSmallScreen">
                                        <div class="btnContinueContent">{#str_ButtonContinue#}</div>
                                    </div>

                                </div>

                            </div>

                        </div> <!-- contentHolder -->

                    </div> <!-- contentVisible -->

                </div> <!-- contentScrollCart -->

            </div> <!-- contentBlocSite -->

        </div> <!-- outerPage -->

        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="ref2" name="ref2" value="{$ref2}" />
            <input type="hidden" id="orderitemid" name="orderitemid" value="{$orderitemid}" />
            <input type="hidden" id="source" name="source" value="{$source}" />
            <input type="hidden" id="webbrandcode" name="webbrandcode" value="{$webbrandcode}" />
            <input type="hidden" id="fsaction" name="fsaction" />
            <input type="hidden" id="password" name="password" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
			<input type="hidden" id="format" name="format" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>
