<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>{$appname} - {#str_TitleEmailUpdated#}</title>

		{if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="includes/customerinclude_small.tpl"}
        <script type="text/javascript" {$nonce}>
{literal}

		var gScreenWidth = 0;
		var gMaxWidth = 600;
		var gOuterBox = 0;
		var gContentScrollCart = 0;
		var gOuterBoxPadding = 0;
		var gOuterBoxContentBloc = 0;

		window.onload = function()
        {
			// set th id for the form resize
			gActiveResizeFormID = new Date().getTime();

            // load the main page
            initializeSmallScreenVersion(true);
		}

		window.addEventListener('DOMContentLoaded', function(e) {
			document.body.onresize = function(e) {
				resizeApp();
			};

			document.body.addEventListener('click', decoratorListener);
		});

		function initializeApplication()
		{
			// main Bloc Size
			var width = document.body.offsetWidth;

			gScreenWidth = width;
			gScreenHeight = document.body.offsetHeight;

			document.getElementById('contentBlocSite').style.width = (width * 2) + 'px';
			document.getElementById('contentPanelMain').style.width = width + 'px';
			document.getElementById('contentPanelRight').style.width = width + 'px';

			var contentScrollCart = document.getElementById('contentScrollCart');
			var styleContentScrollCart = contentScrollCart.currentStyle || window.getComputedStyle(contentScrollCart);
			gContentScrollCart = parseInt(styleContentScrollCart.paddingLeft) + parseInt(styleContentScrollCart.paddingRight);

			var outerBox = document.getElementById('outerBox');
			var styleOuterBox = outerBox.currentStyle || window.getComputedStyle(outerBox);
			gOuterBox = parseIntStyle(styleOuterBox.paddingLeft) + parseIntStyle(styleOuterBox.paddingRight);
			gOuterBox += parseIntStyle(styleOuterBox.borderLeftWidth) + parseIntStyle(styleOuterBox.borderRightWidth);

			var outerBoxPadding = document.getElementById('outerBoxPadding');
			var styleOuterBoxPadding = outerBoxPadding.currentStyle || window.getComputedStyle(outerBoxPadding);
			gOuterBoxPadding = parseInt(styleOuterBoxPadding.paddingLeft) + parseInt(styleOuterBoxPadding.paddingRight);
			gOuterBoxPadding += parseInt(styleOuterBoxPadding.marginLeft) + parseInt(styleOuterBoxPadding.marginRight);

			if (gScreenWidth > gMaxWidth)
			{
				gOuterBoxContentBloc = gMaxWidth - gOuterBox - gOuterBoxPadding;
			}
			else
			{
				gOuterBoxContentBloc = gScreenWidth - gContentScrollCart - gOuterBox - gOuterBoxPadding;
			}
		}

		function initializeSmallScreenVersion(pInit)
		{
			if (pInit)
			{
				//show loading dialog
				showLoadingDialog();
			}

			// set the application panel size
			initializeApplication();

			document.getElementById('contentPanelMain').style.display = 'block';

			// add scrollbar if needed
			setScrollAreaHeight('contentLeftScrollLogin', '');

			// close loading dialog
			closeLoadingDialog();
		}

        function signBackIn()
        {
            document.getElementById("submitform").submit();
        }

		// wrapper for changeSystemLanguageSmallScreen
		function fnChangeSystemLanguageSmallScreen(pElement)
		{
			if (!pElement) {
				return false;
			}

			return changeSystemLanguageSmallScreen(pElement.getAttribute('data-code'));
		}

		function resizeApplication()
		{
			gScreenWidth = 0;

			// set the id for the form reisze
			gActiveResizeFormID = new Date().getTime();

            initializeSmallScreenVersion(false);

			if (gDialogStatus == 'open')
			{
				setDialogPosition();
			}
		}

/**
		* changeSystemLanguageSmallScreen
		*
		* Set the new language and reload the page
		*/
		function changeSystemLanguageSmallScreen(pLanguageCode)
		{
			createCookie("maweblocale", pLanguageCode, 24 * 365);
			document.submitform.fsaction.value = "Welcome.resetPassword";
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

{/literal}
    </script>

    </head>
    <body>

        <noscript>
            <div class="messageNoScriptLarge">
                {#str_ErrorNoJavaScript#}
            </div>
        </noscript>

        <!-- DIALOGS -->

        <div id="dialogOuter" class="dialogOuter"></div>

        <div id="dialogLoading" class="dialogLoading">
          <img class="loadingImage" src="{$webroot}/images/mobile_loading.png" alt=""/>
        </div>

        <!-- END DIALOGS -->

        <!-- HIDDEN DIV TO ACCESS STYLE -->

        <div id="contentScrollCart" class="contentScrollCart hide"></div>
        <div id="outerBox" class="outerBox hide"></div>
        <div id="outerBoxPadding" class="outerBoxPadding hide"></div>
        <div id="currentBloc" class="currentBloc hide"></div>
        <div id="dropDownGeneric" class="wizard-dropdown hide"></div>

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
                <div id="contentPanelMain" class="contentLeftPanel">

                    <div id="contentLeftScrollLogin" class="contentScrollCart">

                        <div class="contentVisible">

								<div class="message-wrap">

									<h1>{#str_TitleEmailUpdated#}</h1>
									<p>{$emailUpdatedMessage}</p>

								</div>
                                
                                <div class="paddingBtnBottomPage">
                                    <div class="btnAction btnContinue" data-decorator="signBackIn">
                                        <div class="btnContinueContent">{#str_ButtonSignBackIn#}</div>
                                    </div>
                                </div>

                            </div> <!-- contentHolder -->

                        </div> <!-- contentVisible -->

                    </div> <!-- contentScrollCart -->

                </div> <!-- contentLeftPanel -->

                <div id="contentPanelRight" class="contentRightPanel">

                </div> <!-- contentPanelForgotPassword -->

                <div class="clear"></div>

            </div> <!-- contentBlocSite -->

        </div> <!-- outerPage -->

        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="fsaction" name="fsaction" value="{$action}" />
        </form>

    </body>
</html>