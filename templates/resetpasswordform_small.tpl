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
{literal}

		var gActivePanel = '';
		var gScreenWidth = 0;
		var gMaxWidth = 600;
		var gOuterBox = 0;
		var gContentScrollCart = 0;
		var gOuterBoxPadding = 0;
		var gOuterBoxContentBloc = 0;
		var gPreviousHash = '';
        var gAlerts = 0;

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

		window.addEventListener('DOMContentLoaded', function(e) {
            document.body.onresize = function(e) {
              resizeApp();
            };

		    document.body.addEventListener('click', decoratorListener);
		    document.body.addEventListener('keypress', decoratorListener);
        });

		window.onload = function()
        {
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
						case "#resetpasswordsubmit":
							validateDataEntry();
							break;
						case "#forgotpassword":
							validateDataEntryForgotPassword();
							break;
					}
				}
			}, false);


			// Load the panel requested compare to the hash in url
			if ((window.location.hash != '') && (window.location.hash == '#forgotpassword'))
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
				}

				gPreviousHash = window.location.hash;
			}
			else
			{
				// load the main page
				initializeSmallScreenVersion(true);
			}

			// Add listener to show/hide password.
			var toggleNewPasswordElement = document.getElementById('togglenewpassword');
			if (toggleNewPasswordElement)
			{
				toggleNewPasswordElement.addEventListener('click', function() {
					togglePasswordVisibility(toggleNewPasswordElement, 'newpassword');
				});
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
					showPanelAccount(false, '');
					break;
				case '#forgotpassword':
					showPanelAccount(false, 'contentPanelMain');
					setScrollAreaHeight('contentScrollForgotPassword', '');
				case '#resetpasswordsubmit':
					showPanelAccount(false, 'contentPanelMain');
					break;
			}

			gPreviousHash = window.location.hash;
		}

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
                document.getElementById(pDivID).style.display = 'block';

                // slide to the panel
                switch(pDivID)
                {
                    case 'contentPanelRight':

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

		/**
		* processAjaxSmallScreen
		*
		* send an ajax query to the server and get the respond back
		*/
	   function processAjaxSmallScreen(obj, serverPage, requestMethod, params)
	   {
           if ('POST' === requestMethod) {
               // Add CSRF token to post submissions
               var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
               if (csrfMeta) {
                   var csrfToken = csrfMeta.getAttribute('content');

                   if (typeof params !== 'undefined' && null !== params && params.length > 0) {
                       params += '&csrf_token=' + csrfToken;
                   } else {
                       params = 'csrf_token=' + csrfToken;
                   }
               }
           }

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

			xmlhttp.onreadystatechange = function()
			{
				if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
				{
					switch (obj)
					{
						case 'forgotpassword':
							var jsonObj = parseJson(xmlhttp.responseText);

							//update the gift card balance
							document.getElementById('contentPanelMain').innerHTML = jsonObj.template;

							document.getElementById('contentNavigationForgotPassword').classList.add('hide');

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
								closeLoadingDialog();

								document.getElementById('contentPanelMain').innerHTML = jsonObj.template;

								setHashUrl(obj);
							}
							break;
						case 'resetpasswordsubmit':
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
                                closeLoadingDialog();

                                document.getElementById('contentPanelMain').innerHTML = jsonObj.template;

                                setHashUrl(obj);
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
					showPanelAccount(false, 'contentPanelMain');
				case '#forgotpasswordsubmit':
					initializeApplication();
					showPanelAccount(false, 'contentPanelMain');
					break;
				case '#resetpasswordsubmit':
					initializeApplication();
					showPanelAccount(false, 'contentPanelMain');
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
				case '#resetpasswordsubmit':
					setContentVisibleHeight('contentScrollForgotPassword');
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

		function highlight(field)
		{
			var inputObj = document.getElementById(field);
			if (inputObj)
			{
				inputObj.className = inputObj.className + ' errorInput';
				gAlerts = 1;
			}
		}

		// wrapper for validateDataEntry
		function fnValidateDataEntry(pElement)
        {
            if (!pElement)
            {
                return false;
            }

            return validateDataEntry();
        }

		function validateDataEntry()
		{
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
				createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), function(e) {
				    closeDialog(e);
                });
			}
			else
			{
				var format = ((document.location.protocol != 'https:') ? 1 : 0);

				// show loading dialog
				showLoadingDialog();

				// send the ajax
				postParams = '&format=' + format;
				postParams += '&data2=' + ((format == 0) ? newpassword.value : hex_md5(newpassword.value));
				postParams += '&data3=' + '{/literal}{$requesttoken}{literal}';
				postParams += '&mobile=true';

                // Add CSRF token to post submissions
                var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
                if (csrfMeta) {
                    postParams += '&csrf_token=' + csrfMeta.getAttribute('content');
                }

				processAjaxSmallScreen("resetpasswordsubmit",".?fsaction=Welcome.resetPasswordProcess", 'POST', postParams);
			}
		}

		function validateDataEntryForgotPassword()
		{
			//Get the username that has been input
			var userNameTmp = document.getElementById('loginForgotPassword').value;

			gAlerts = 0;
			var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
			var loginValue = document.getElementById('loginForgotPassword').value;
			var emailValue = document.getElementById('emailForgotPassword').value;
			var resetRequestFormToken = document.getElementById('passwordresetrequesttoken').value;

			if (loginValue.length == 0)
			{
				message += "<br />{/literal}{#str_ErrorNoUserName#}{literal}";
				highlight("loginForgotPassword");
			}

			if (emailValue.length == 0)
			{
				message += "<br />{/literal}{#str_ErrorNoEmailAddress#}{literal}";
				highlight("emailForgotPassword");
			}
			else
			{
				if (!validateEmailAddress(emailValue))
				{
					message += "<br />{/literal}{#str_MessageCompulsoryEmaiInvalid#}{literal}";
					highlight("emailForgotPassword");
				}
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
				postParams += '&email=' + emailValue;
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

		function initializeForgotPassword()
		{
			setScrollAreaHeight('contentScrollForgotPassword', '');
		}

		function fnHandlePasswordStrength(pElement)
        {
            if (!pElement)
            {
				return false;
            }

            return passwordStrength.scorePassword(pElement.value, 'strengthvalue', 'strengthtext');
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

                            <div class="pageLabel">

                                {#str_LabelForgottenPassword#}

                            </div> <!-- pageLabel -->

                            <div id="contentHolder">
								<div id="changePasswordForm" class="outerBox outerBoxPadding">
								<div>

									<div class="formLine1">
										<label for="newpassword">{#str_LabelNewPassword#}: </label>
										<img src="{$brandroot}/images/asterisk.png" alt="*"/>
									</div>

									<div class="formLine2">
										<div class="password-input-wrap">
											<input type="password" id="newpassword" name="newpassword" value="" class="middle" data-decorator="fnHandlePasswordStrength" />
											<button type="button" id="togglenewpassword" class="password-visibility password-show"></button>
											<img class="error_form_image" id="newpasswordcompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
											<div class="progress-wrap">
												<progress id="strengthvalue" value="0" min="0" max="5"></progress>
												<p>{#str_LabelPasswordStrength#}: <span id="strengthtext">{#str_LabelStartTyping#}</span></p>
											</div>
										</div>
										<div class="clear"></div>
									</div>

								</div>

							</div> <!-- outerBox outerBoxPadding -->

							<div class="paddingBtnBottomPage">
								<div class="btnAction btnContinue" data-decorator="fnValidateDataEntry">
									<div class="btnContinueContent">{#str_ButtonSaveNewPassword#}</div>
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

        <form id="submitform" name="submitform" method="post" action="" accept-charset="utf-8">
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="mobile" name="mobile" value="true" />
			<input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>