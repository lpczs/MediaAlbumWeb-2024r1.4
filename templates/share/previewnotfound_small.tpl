<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
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
        {include file="order/finalstageincludedecorator.tpl"}

        <script type="text/javascript" {$nonce}>
                //<![CDATA[
{literal}

        var gDialogIsOpen = false;
        var gMaxWidth = 600;

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

            initializeSmallScreenVersion();
        }

        window.reisze = function()
        {
            resizeApp();
        }

        /**
        * resizeApp
        *
        * resize the application when the device is rotated
        */
        function resizeApp()
        {
            //prompt the loading dialog
            showLoadingDialog();

            initializeSmallScreenVersion();

            if (gDialogStatus == 'open')
            {
                setDialogPosition();
            }
        }

        function initializeSmallScreenVersion()
        {
            //show loading dialog
            showLoadingDialog();

            // add scrollbar if needed
            setScrollAreaHeight('contentLeftScrollPreview', '');

            // close loading dialog
            closeLoadingDialog();
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

               <div id="contentLeftScrollPreview" class="contentScrollCart">

                   <div class="contentVisible">

                        <div class="pageLabel">

                            {#str_LabelPreviewNotFound#}

                        </div> <!-- pageLabel -->

                        <noscript>
                            <div class="messageNoScriptLarge">
                                {#str_ErrorNoJavaScript#}
                            </div>
                        </noscript>

                        <div id="contentHolder">

                            <div class="outerBox outerBoxPadding">

                                <div class="informationText">
                                    {#str_LabelPreviewNotFoundMessage#}
                                </div>

                            {if $mainwebsiteurl != ''}

                                <div class="btnRightContinue">
                                   <div class="btnAction btnContinue" data-decorator="fnRedirect" data-url="{$mainwebsiteurl|escape}">
                                        <div class="btnContinueContent">{#str_ButtonContinue#}</div>
                                    </div>
                                </div>
                                <div class="clear"></div>

                            {/if}

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
            <input type="hidden" id="fsaction" name="fsaction" value="Share.preview" />
        </form>

    </body>
</html>
