<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>{$appname} - {#str_LabelWelcome#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="includes/customerinclude_small.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

    {literal}
            // laoding image
            var gActivePanel = '';
            var gScreenWidth = 0;
            var gMaxWidth = 600;
            var gOuterBox = 0;
            var gContentScrollCart = 0;
            var gOuterBoxPadding = 0;
            var gOuterBoxContentBloc = 0;
            var gPreviousHash = '';
            var gAlerts = 0;


            window.addEventListener('DOMContentLoaded', function(e) {
                document.body.onresize = function(e) {
                    resizeApp();
                };

                document.body.addEventListener('click', decoratorListener);
                document.body.addEventListener('keyup', decoratorListener);
                document.body.addEventListener('change', decoratorListener);
            });

            // wrapper method for setHashUrl
            function fnSetHashUrl(pElement)
            {
                if(!pElement)
                {
                    return false;
                }

                return setHashUrl(pElement.getAttribute('data-hash-url'));
            }

            // wrapper function for validateLoginSmallScreen
            function fnValidateLoginSmallScreen(pElement)
            {
                return validateLoginSmallScreen();
            }

            // wrapper for changeSystemLanguageSmallScreen
            function fnChangeSystemLanguageSmallScreen(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return changeSystemLanguageSmallScreen(pElement.getAttribute('data-code'));
            }

            // wrapper for showForgotPassword
            function fnShowForgotPassword(pElement)
            {
                return showForgotPassword();
            }

            //wrapper for passwordStrength
            function fnHandlePasswordStrength(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return passwordStrength.scorePassword(pElement.value, 'strengthvalue', 'strengthtext');
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

            // init page element handlers
            function initEventHandlers()
            {
                var showAccountElement = document.getElementById('showNewAccount');
                if (showAccountElement)
                {
                    document.getElementById('showNewAccount').onclick = showNewAccount;
                }

				var togglePassword2SmallElement = document.getElementById('togglepassword2small');
                if (togglePassword2SmallElement)
                {
					togglePassword2SmallElement.addEventListener('click', function() {
                        togglePasswordVisibility(togglePassword2SmallElement, 'password2small');
                    });
                }
            }

            window.onload = function()
            {
                // init the event handlers
                initEventHandlers();

                /* set a cookie to store the local time */
                var theDate = new Date();
                createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

                /* Create the device type string and then set the cookie */
                var devString = makeDevCookie();
                createCookie("mawdd", devString, 90000);

                // listen hash url changes
                window.onhashchange = locationHashChanged;

                // set th id for the form resize
                gActiveResizeFormID = new Date().getTime();

                //function to handle the pressing of the go button in android and return in ios also on keyboard
                addEventListener("keydown", function (e)
                {
                    if (e.keyCode == 13)
                    {
                        // execute specific action compare to the form a user is on
                        switch(window.location.hash)
                        {
                            case "":
                            case "#":
                                validateLoginSmallScreen();
                                break;
                            case "#createaccount":
                                verifyAddress();
                                break;
                            case "#forgotpassword":
                                validateDataEntryForgotPassword();
                                break;
                        }
                    }
                }, false);

                // Load the panel requested compare to the hash in url
                if ((window.location.hash != '') && ((window.location.hash == '#forgotpassword') || (window.location.hash == '#createaccount')))
                {
                    // main Bloc Size
                    initializeApplication();
                    document.getElementById('contentPanelMain').style.marginLeft = (gScreenWidth * -1) + 'px';
                    document.getElementById('contentPanelRight').style.display = 'block';
                    document.getElementById('contentPanelMain').style.display = 'block';

                    gActivePanel = 'contentPanelRight';

                    switch(window.location.hash)
                    {
                        case '#forgotpassword':
                            showForgotPassword();
                            break;
                        case '#createaccount':
                            showNewAccount();
                            break;
                    }

                    gPreviousHash = window.location.hash;
                }
                else
                {
                    // load the main page
                    initializeSmallScreenVersion(true);
                }
            }

            /**
            * locationHashChanged
            *
            * Actions executed on back or forward buttons
            */
            function locationHashChanged()
            {
                // execute specific action compare to the hash of the url
                switch(window.location.hash)
                {
                    case '':
                    case '#':
                        if (gPreviousHash == '#forgotpassword')
                        {
                            updateUserName('forgotpassword');
                        }

                        showPanelAccount(false, '');
                        break;
                    case '#forgotpassword':
                    case '#forgotpasswordsubmit':
                    case '#createaccount':
                        showPanelAccount(true, 'contentPanelRight');
                        break;
                }

                gPreviousHash = window.location.hash;
            }

            function updateUserName(pCurrentForm)
            {
                if (pCurrentForm == 'login')
                {
                    document.getElementById('loginForgotPassword').value = document.getElementById('login2small').value;
                }
                else if (pCurrentForm == 'forgotpassword')
                {
                    document.getElementById('login2small').value = document.getElementById('loginForgotPassword').value;
                }
            }

            /**
            * setHashUrl
            *
            * set the hash of the url
            */
            function setHashUrl(pHash)
            {
                // set the id for the form reisze
                gActiveResizeFormID = new Date().getTime();

                window.location.hash = pHash;
            }

            function resizeApplication()
            {
                gScreenWidth = 0;

                // set the id for the form reisze
                gActiveResizeFormID = new Date().getTime();

                switch(gPreviousHash)
                {
                    case '':
                    case '#':
                        initializeSmallScreenVersion(false);
                        break;

                    case '#forgotpassword':
                        initializeApplication();
                        initializeForgotPassword();
                        showPanelAccount(true, 'contentPanelRight');
                        break;

                    case '#createaccount':
                        initializeApplication();
                        initiliazeNewAccount(true);
                        showPanelAccount(true, 'contentPanelRight');
                        break;
                    case '#forgotpasswordsubmit':
						initializeApplication();
						showPanelAccount(true, 'contentPanelRight');
                    	break;
                }

                if (gDialogStatus == 'open')
                {
                    setDialogPosition();
                }
            }

            function onKeyboardAction()
            {
                switch(gPreviousHash)
                {
                    case '':
                    case '#':
                        setContentVisibleHeight('contentLeftScrollLogin');
                        break;

                    case '#forgotpassword':
                        setContentVisibleHeight('contentScrollForgotPassword');
                        break;

                    case '#createaccount':
                        setContentVisibleHeight('contentScrollNewAccount');
                        break;
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
                document.submitform.fsaction.value = "{/literal}{$changelanguageinitfsaction}{literal}";
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
                gouterBox = parseIntStyle(styleOuterBox.paddingLeft) + parseIntStyle(styleOuterBox.paddingRight);
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

                // if an error, display dialog
        {/literal}

            {if $error != ''}

                {literal}

                if (pInit == true)
                {
                    createDialog("{/literal}{#str_TitleError#}{literal}", "{/literal}{$error}{literal}",function(e){
                        closeDialog(e);
                    });
                }

                {/literal}

            {/if}

        {literal}

                document.getElementById('contentPanelMain').style.display = 'block';

                // add scrollbar if needed
                setScrollAreaHeight('contentLeftScrollLogin', '');

                // close loading dialog
                closeLoadingDialog();
            }

            function validateLoginSmallScreen()
            {
                //Get the username that has been input
                var userName = document.getElementById('login2small').value;

                gAlerts = 0;
                var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
                if (userName.length == 0)
                {
                    message += "<br />{/literal}{#str_ErrorNoUserName#}{literal}";
                    highlight('login2small');
                }

                if (document.getElementById('password2small').value.length == 0)
                {
                    message += "<br />{/literal}{#str_ErrorNoPassword#}{literal}";
                    highlight('password2small');
                }

                if (gAlerts == 1)
                {
                    createDialog("{/literal}{#str_TitleWarning#}{literal}", message, function(e) {
                        closeDialog(e);
                    });
                }
                else
                {
                    var format = ((document.location.protocol != 'https:') ? 1 : 0);
                    var submitForm =document.getElementById("submitform");

                    /* copy the values into the form we will submit and then submit it to the server */
                    submitForm.mobile.value = true;
                    submitForm.login.value = userName;
                    submitForm.password.value = ((format == 0) ? document.getElementById('password2small').value : hex_md5(document.getElementById('password2small').value));
                    submitForm.fsaction.value = "{/literal}{$loginfsaction}{literal}";
                    submitForm.format.value = format;
                    submitForm.prtz.value = '{/literal}{$prtz}{literal}';
                    submitForm.mawebhluid.value = '{/literal}{$mawebhluid}{literal}';
                    submitForm.mawebhlbr.value = '{/literal}{$mawebhlbr}{literal}';
                    submitForm.fhlbu.value = '{/literal}{$fhlbu}{literal}';


                    submitForm.submit();
                }

                // set the username that was in the input
               document.getElementById('login2small').value = userName;
            }

            /**
            * processAjaxSmallScreen
            *
            * send an ajax query to the server and get the respond back
            */
           function processAjaxSmallScreen(obj, serverPage, requestMethod, params)
           {
               /* get an XMLHttpRequest object for use */
               /* make xmlhttp local so we can run simlutaneous requests */
               var xmlhttp = getxmlhttpSmallScreen();
               if (requestMethod == 'GET')
               {
                   xmlhttp.open("GET", serverPage+"&dummy=" + new Date().getTime(), true);
               }
               else
               {
                   xmlhttp.open("POST", serverPage, true);
                   xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
               }

               // check if the query was a GET call
               if (requestMethod == 'GET')
               {
                   xmlhttp.onreadystatechange = function()
                   {
                        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                        {
                            switch (obj){
                                case 'termsandconditionswindow':
                                    // show a dialog bowx with the HTML from the server
                                    createDialog("{/literal}{#str_TitleTermsAndConditions#}{literal}", unescape(xmlhttp.responseText), function(e) {
                                        closeDialog(e);
                                    });
                                break;
                            }
                        }
                        else if ((xmlhttp.readyState == 4) && ((xmlhttp.status == 405) || (xmlhttp.status == 403)))
                        {
                            handleSecurityError(xmlhttp.responseText);
                        }
                   };
                   xmlhttp.send(null);
               }
               else
               {
                    xmlhttp.onreadystatechange = function()
                    {
                        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                        {
                           switch (obj)
                           {
                                case 'forgotpassword':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    //update the gift card balance
                                    document.getElementById('contentPanelRight').innerHTML = jsonObj.template;

                                    // pass the login username value to the forgotten password username
                                    updateUserName('login');

                                    if (gPreviousHash == '#forgotpassword')
                                    {
                                        initializeForgotPassword();
                                        // close the loding dialog
                                        closeLoadingDialog();
                                    }
                                    else
                                    {
                                        setHashUrl(obj);
                                    }
                                    break;

                                case 'forgotpasswordsubmit':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.error != '')
                                    {
                                        closeLoadingDialog();

                                        createDialog("{/literal}{#str_TitleError#}{literal}", jsonObj.error, function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                    else
                                    {
                                        if (jsonObj.redirecturl != '')
                                        {
                        					window.location.replace(jsonObj.redirecturl);
                                        }
                                        else
                                        {
                                        	closeLoadingDialog();

                                        	document.getElementById('contentPanelRight').innerHTML = jsonObj.template;

                                        	setHashUrl(obj);
                                        }
                                    }
                                    break;
                                case 'createaccount':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    //update the gift card balance
                                    document.getElementById('contentPanelRight').innerHTML = jsonObj.template;

                                    // copy javascript content
                                    toggleJs('includeNewAccountJS', jsonObj.javascript, true, '', '{/literal}{$nonceraw}{literal}');


                                    if (gPreviousHash == '#createaccount')
                                    {
                                        initiliazeNewAccount(true);
                                        // close the loding dialog
                                        closeLoadingDialog();
                                    }
                                    else
                                    {
                                        setHashUrl(obj);
                                    }
                                    break;

                                case 'createaccountsubmit':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.error != '')
                                    {
                                        closeLoadingDialog();

                                        createDialog("{/literal}{#str_TitleError#}{literal}", jsonObj.error, function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                    else
                                    {
                                        // submit the porcess login form
                                        var newAccountForm = document.getElementById("submitformNewAccount");
                                        var format = ((document.location.protocol != 'https:') ? 1 : 0);

                                        var submitForm = document.getElementById("submitform");

                                        /* copy the values into the form we will submit and then submit it to the server */
                                        submitForm.action = window.location.href.replace(window.location.hash, '');
                                        submitForm.mobile.value = true;
                                        submitForm.login.value = newAccountForm.login.value;
                                        submitForm.password.value = newAccountForm.password.value;

                                       {/literal}{if $ishighlevel == 1}{literal}
                                        	submitForm.fsaction.value = "OnlineAPI.processLogin";

                                        {/literal}{else}{literal}
                                        	submitForm.fsaction.value = "Welcome.processLogin";
                                       	{/literal}{/if}{literal}
                        				submitForm.format.value = format;
                        				submitForm.prtz.value = '{/literal}{$prtz}{literal}';
                        				submitForm.mawebhluid.value = '{/literal}{$mawebhluid}{literal}';
                        				submitForm.mawebhlbr.value = '{/literal}{$mawebhlbr}{literal}';
                        				submitForm.fhlbu.value = '{/literal}{$fhlbu}{literal}';

                                        submitForm.submit();
                                    }
                                    break;
                           }
                        }
                        else if ((xmlhttp.readyState == 4) && ((xmlhttp.status == 405) || (xmlhttp.status == 403)))
                        {
                            handleSecurityError(xmlhttp.responseText);
                        }
                    }
                    xmlhttp.send(params);
               }
            }

        {/literal}

            {if $resetpasswordenabled}

                {literal}

            function initializeForgotPassword()
            {
                setScrollAreaHeight('contentScrollForgotPassword', 'contentNavigationForgotPassword');
            }

            function showForgotPassword()
            {
                // show loading dialog
                showLoadingDialog();

                var postParams = '&mobile=true';

                // Add CSRF token to post submissions
                var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
                if (csrfMeta) {
                    postParams += '&csrf_token=' + csrfMeta.getAttribute('content');
                }

                // send the ajax
                processAjaxSmallScreen('forgotpassword',".?fsaction=Welcome.initForgotPassword&ref={/literal}{$ref}{literal}", 'POST', postParams);
            }

            function validateDataEntryForgotPassword()
            {
                //Get the username that has been input
                var userNameTmp = document.getElementById('loginForgotPassword').value;

                gAlerts = 0;
                var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
                var loginValue = document.getElementById('loginForgotPassword').value;
                var resetRequestFormToken = document.getElementById('passwordresetrequesttoken').value;

                if (loginValue.length == 0)
                {
                    message += "<br />{/literal}{#str_ErrorNoUserName#}{literal}";
                    highlight("loginForgotPassword");
                }

                if (gAlerts > 0)
                {
                    createDialog("{/literal}{#str_TitleWarning#}{literal}", message, function(e) {
                        closeDialog(e);
                    });
                }
                else
                {
                    // show loading dialog
                    showLoadingDialog();

                    /* copy the values into the form we will submit and then submit it to the server */
                    var postParams = '&login=' + loginValue;
                    postParams += '&mobile=true';
                    postParams += '&format=' + ((document.location.protocol != 'https:') ? 1 : 0);
                    postParams += '&passwordresetrequesttoken=' + resetRequestFormToken;

                    // Add CSRF token to post submissions
                    var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
                    if (csrfMeta) {
                        postParams += '&csrf_token=' + csrfMeta.getAttribute('content');
                    }

                    // send the ajax
                    processAjaxSmallScreen("forgotpasswordsubmit",".?fsaction=Welcome.resetPasswordRequest&mobile=true&ref={/literal}{$session}{literal}", 'POST', postParams);

                }

                //Set the username that was input
                document.getElementById("loginForgotPassword").setAttribute("value", userNameTmp);
            }

                {/literal}

            {/if} {* end {if $resetpasswordenabled} *}


            {if $login2Template!= ''}

                {literal}

            function showNewAccount()
            {
                // show loading dialog
                showLoadingDialog();

                // Add CSRF token to post submissions
                var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
                if (csrfMeta) {
                    var csrfToken = csrfMeta.getAttribute('content');
                }

                var postParam = '&mobile=true';

                {/literal}{if $ishighlevel == 1}{literal}
            		postParam += "&groupcode={/literal}{$groupcode_script}{literal}";
            	{/literal}{/if}{literal}

            		postParam += "&prtz={/literal}{$prtz}{literal}";
            		postParam += "&mawebhluid={/literal}{$mawebhluid}{literal}";
            		postParam += "&mawebhlbr={/literal}{$mawebhlbr}{literal}";
            		postParam += "&ishighlevel={/literal}{$ishighlevel}{literal}";
            		postParam += "&registerfsaction={/literal}{$registerfsaction}{literal}";
            		postParam += "&fromregisterlink={/literal}{$fromregisterlink}{literal}";
                    postParam += '&csrf_token=' + csrfToken;


                // send the ajax
                processAjaxSmallScreen('createaccount',".?fsaction=Welcome.initNewAccount&ref={/literal}{$ref}{literal}", 'POST', postParam);
            }

            function fnCJKHalfWidthFullWidthToASCII(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return CJKHalfWidthFullWidthToASCII(pElement, JSON.parse(pElement.getAttribute('data-force-uppercase')));
            }

            /**
            * initializeNewAccountSmallScreen
            *
            * Set the htlm elements of the address from
            */
           function initializeNewAccountSmallScreen()
           {
                // tick content for marketing list
                var width = gOuterBoxContentBloc;

                var marketingList = document.getElementById('marketingList');
                var styleMarketingList = marketingList.currentStyle || window.getComputedStyle(marketingList);
                width = width - parseIntStyle(styleMarketingList.paddingLeft) - parseIntStyle(styleMarketingList.paddingRight);

                // set the width of the marketing list
                var classLength = document.getElementsByClassName('listLabel').length;
                for (var i = 0; i < classLength; i++)
                {
                    var elm = document.getElementsByClassName('listLabel')[i];

                    if (i == 0)
                    {
                        var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                        width = width - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                    }

                    elm.style.width = width + 'px';
                }

                // terms and conditions label
                var termsAndConditions = document.getElementById('labelTermsAndConditions');
                if (termsAndConditions)
                {
                    var orderTermsAndConditions = document.getElementById('ordertermsandconditions');
                    var styleOrderTermsAndConditions = orderTermsAndConditions.currentStyle || window.getComputedStyle(orderTermsAndConditions);
                    var checkBoxWidth = parseIntStyle(styleOrderTermsAndConditions.width) + parseIntStyle(styleOrderTermsAndConditions.paddingLeft) + parseIntStyle(styleMarketingList.paddingRight);
                    checkBoxWidth = checkBoxWidth + parseIntStyle(styleOrderTermsAndConditions.marginLeft) + parseIntStyle(styleOrderTermsAndConditions.marginRight);

                    document.getElementById('labelTermsAndConditions').style.width = (gOuterBoxContentBloc - checkBoxWidth) + 'px';
                }

                setScrollAreaHeight('contentScrollNewAccount', 'contentNavigationNewAccount');
            }

                {/literal}

            {/if}

        {literal}

            /**
             * showPanelAccount
             *
             * slide from a panel to another panel
             */
            function showPanelAccount(pVisible, pDivID)
            {
                // main panel
                if (pVisible)
                {
                    // force the panel to be visible
                    document.getElementById(pDivID).style.display= 'block';

                    // slide to the panel
                    switch(pDivID)
                    {
                        case 'contentPanelRight':

                            switch(window.location.hash)
                            {
                                case '#forgotpassword':
                                    initializeForgotPassword();
                                    break;
                                case '#createaccount':
                                    initiliazeNewAccount(true);
                                    break;
                            }

                            // slide to the right panel
                            document.getElementById('contentPanelMain').style.marginLeft = '-' + gScreenWidth + 'px';
                            break;
                    }

                   gActivePanel = pDivID;
                }
                else
                {
                    // slide back to a panel
                    switch(gActivePanel)
                    {
                        case 'contentPanelRight':

                            // add scrollbar if needed
                            setScrollAreaHeight('contentLeftScrollLogin', '');

                            // slide back to the left panel
                            document.getElementById('contentPanelMain').style.marginLeft = 0;

                            // delay to hide the panel to make sure the other panel is displayed (css animation)
                            setTimeout(function()
                            {
                                // hide the previous panel
                                document.getElementById(gActivePanel).style.display= 'none';
                                gActivePanel = '';
                            }, 300);
                            break;
                    }
                }

                // close the loding dialog
                closeLoadingDialog();
            }

            {/literal}
                //]]>
        </script>

    </head>
    <body>

        <noscript>
            <div class="messageNoScriptLarge">
                {#str_ErrorNoJavaScript#}
            </div>
        </noscript>


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

                            <div class="pageLabel">

                                {#str_TitleSignIn#}

                            </div> <!-- pageLabel -->

                            <div id="contentHolder">

                                <div id="blocSignIn" class="outerBox outerBoxPadding">

                                    <div>

                                        <div class="formLine1">
                                            <label for="login2small">{#str_LabelEmailorUsername#}:</label>
                                            <img class="valign-center" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                        </div>

                                        <div class="formLine2">
                                            <input type="text" id="login2small" name="login2small" value="{$loginVal}" class="middle" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="top_gap">

                                        <div class="formLine1">
                                            <label for="password2small">{#str_LabelPassword#}:</label>
                                            <img class="valign-center" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                        </div>

                                        <div class="formLine2 password-input-wrap">
                                            <input type="password" id="password2small" name="password2small" value="" class="middle" />
											<button type="button" id="togglepassword2small" class="password-visibility password-show"></button>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                </div> <!-- blocSignIn -->

                                 <div class="signInBtn" data-decorator="fnValidateLoginSmallScreen">

                                    <div class="btnAction btnContinue">
                                        <div class="btnContinueContent">{#str_ButtonSignIn#}</div>
                                    </div>

                                </div>

                                {if $resetpasswordenabled}

                                    <div class="linkAction" data-decorator="fnShowForgotPassword">

                                        <div class="changeBtnText">
                                            {#str_LabelForgotPassword#}
                                        </div>

                                        <div class="changeBtnImg">
                                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                                        </div>

                                        <div class="clear"></div>

                                    </div> <!-- linkAction -->


                                {/if} {* end {if $resetpasswordenabled} *}


                            </div> <!-- contentHolder -->

                            {if $login2Template != ''}

                                {include file="$login2Template"}

                            {/if}

                        </div> <!-- contentVisible -->

                    </div> <!-- contentScrollCart -->

                </div> <!-- contentLeftPanel -->

                <div id="contentPanelRight" class="contentRightPanel">

                </div> <!-- contentPanelForgotPassword -->

                <div class="clear"></div>

            </div> <!-- contentBlocSite -->

        </div> <!-- outerPage -->

        <form id="submitform" name="submitform" method="post" action="" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />

            {if $ishighlevel == 1}
            	<input type="hidden" id="groupcode" name="groupcode" value="{$groupcode}" />
            {/if}

            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="login" name="login" />
            <input type="hidden" id="password" name="password" />
            <input type="hidden" id="format" name="format" />
            <input type="hidden" id="prtz" name="prtz" />
            <input type="hidden" id="mawebhluid" name="mawebhluid" />
            <input type="hidden" id="mawebhlbr" name="mawebhlbr" />
            <input type="hidden" id="fhlbu" name="fhlbu" />
            <input type="hidden" id="ishighlevel" name="ishighlevel" value="{$ishighlevel}" />
            <input type="hidden" id="registerfsaction" name="registerfsaction" value="{$registerfsaction}" />
            <input type="hidden" id="fromregisterlink" name="fromregisterlink" value="{$fromregisterlink}" />
            <input type="hidden" id="mobile" name="mobile" value="true" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>