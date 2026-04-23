<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {$title}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="includes/customerinclude_small.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[
            {literal}

                var gDialogIsOpen = false;
                var gMaxWidth = 600;
                var gCurrentSource = 'updateNewAccount';
                var gScreenWidth = 0;
                var gOuterBoxContentBloc = 0;

                /**
                * changeSystemLanguageSmallScreen
                *
                * Set the new language and reload the page
                */
                function changeSystemLanguageSmallScreen(pLanguageCode)
                {
                    createCookie("maweblocale", pLanguageCode, 24 * 365);
                    document.submitformaddress.fsaction.value = "{/literal}{$refreshaction}{literal}";
                    document.submitformaddress.submit();
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
                };

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

					gScreenHeight = document.body.offsetHeight;

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

                    initializeAddress(true, '');

                    // close loading dialog
                    closeLoadingDialog();
                }

                // wrapper for changeSystemLanguageSmallScreen
                function fnChangeSystemLanguageSmallScreen(pElement)
                {
                    if (!pElement) {
                        return false;
                    }

                    return changeSystemLanguageSmallScreen(pElement.getAttribute('data-code'));
                }
            {/literal}

            //]]>
        </script>

        <script type="text/javascript" id="mainjavascript" {$nonce}>
        //<![CDATA[

            {include file="order/updateaddress.tpl"}

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
        <div id="innerBox" class="innerBox hide"></div>
        <div id="innerBoxPadding" class="innerBoxPadding hide"></div>

        <div id="containerHighLight" class="componentHighLight hide"></div>

        <div id="dropDownGeneric" class="wizard-dropdown hide"></div>

        <!-- END HIDDEN DIV TO ACCESS STYLE -->

        <div id="outerPage" class="outerPage">

            <div id="headerSmall" class="header">
                <div class="headerinside">
                    {include file="header_small.tpl"}
                </div>
            </div>

            <div class="tpxPopupOption" id="language-list-popup">
                <div class="tpx-popup-option-bloc">
                    <div class="tpx-popup-option-content">
                        {$systemlanguagelist}
                    </div>
                </div>
            </div>

            <div class="notificationBar" id="notificationBar">
                <img src="{$brandroot}/images/notifications-confirm.png" alt="" />
                <span id="notificationText"></span>
            </div>

            <div id="contentBlocSite" class="contentBlocSite">

                <div id="loadingGif" class="loadingGif"></div>

                <div id="contentRightScrollAddress" class="contentScrollCart">

                    <div class="contentVisible">

                         <div class="pageLabel">
                             {$title}
                         </div>
                         <div class="outerBox outerBoxPadding outerBoxMarginBottom">
                             <div id="contentAddressForm">
                                 {include file="addressform.tpl"}
                             </div>
                         </div>
                         <div class="btnUpdateLarge" data-decorator="verifyAddress">
                             <div id="updateButtonLarge" class="btnUpdateContent">{#str_ButtonUpdate#}</div>
                         </div>

                     </div> <!-- contentVisible -->

                </div> <!-- contentScrollCart -->

            </div> <!-- contentBlocSite -->

        </div> <!-- outer-page -->

        <div style="display:none">
             <form id="submitformaddress" name="submitformaddress" method="post" accept-charset="utf-8" action="#">
                <input type="hidden" id="ref" name="ref" value="{$ref}" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
                <input type="hidden" id="contactfname" name="contactfname" />
                <input type="hidden" id="contactlname" name="contactlname" />
                <input type="hidden" id="companyname" name="companyname" />
                <input type="hidden" id="address1" name="address1" />
                <input type="hidden" id="address2" name="address2" />
                <input type="hidden" id="address3" name="address3" />
                <input type="hidden" id="address4" name="address4" />
                <input type="hidden" id="add41" name="add41" />
                <input type="hidden" id="add42" name="add42" />
                <input type="hidden" id="add43" name="add43" />
                <input type="hidden" id="city" name="city" />
                <input type="hidden" id="county" name="county" />
                <input type="hidden" id="state" name="state" />
                <input type="hidden" id="regioncode" name="regioncode" />
                <input type="hidden" id="region" name="region" />
                <input type="hidden" id="postcode" name="postcode" />
                <input type="hidden" id="countrycode" name="countrycode" />
                <input type="hidden" id="countryname" name="countryname" />
                <input type="hidden" id="submit_telephonenumber" name="telephonenumber" />
                <input type="hidden" id="submit_email" name="email" />
                <input type="hidden" id="submit_registeredtaxnumbertype" name="registeredtaxnumbertype" />
                <input type="hidden" id="submit_registeredtaxnumber" name="registeredtaxnumber" />
                <input type="hidden" name="previousstage" value="{$previousstage}"/>
                <input type="hidden" name="stage" value="{$stage}"/>
                 <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
            </form>
        </div>
    </body>
</html>