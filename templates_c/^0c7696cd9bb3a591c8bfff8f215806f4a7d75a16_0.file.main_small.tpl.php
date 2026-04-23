<?php
/* Smarty version 4.5.3, created on 2026-03-09 03:44:59
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\main_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ae423b3204b8_74008021',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0c7696cd9bb3a591c8bfff8f215806f4a7d75a16' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\main_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_small.tpl' => 1,
    'file:customer/main.tpl' => 1,
  ),
),false)) {
function content_69ae423b3204b8_74008021 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="csrf-token" content="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>
</title>

         <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

         <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        //<![CDATA[

            <?php $_smarty_tpl->_subTemplateRender("file:customer/main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

            

            var gSession = "<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
";
            var gIsMobile = "<?php echo $_smarty_tpl->tpl_vars['issmallscreen']->value;?>
";
            var gSSOToken = "<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
";
            var gActiveAction = '';
            var gActivePanel = '';
            var gActiveProduct = '';
            var gHistoryProduct = '';
            var gActiveProductOnline = '';
            var gOriginalProductOnline = '';
            var gNameForm = false;
            var gPreviewLoaded = false;
            var gPreviousHash = '';

            // app width
            var gScreenWidth = 0;
            var gMaxWidth = 600;
            var gOuterBox = 0;
            var gContentScrollCart = 0;
            var gOuterBoxPadding = 0;
            var gOuterBoxContentBloc = 0;
            var gSiteContainer = 0;
            var gIsPreviewActive = false;
            var gIsPreviewValid = false;
            var gShareDetailActive = '';

            var gNameFormAction = '';

            var gAddressupdated = "<?php echo $_smarty_tpl->tpl_vars['addressupdated']->value;?>
";
            var isHighLevel = <?php echo $_smarty_tpl->tpl_vars['ishighlevel']->value;?>
;
			var basketRef = "<?php echo $_smarty_tpl->tpl_vars['basketref']->value;?>
";

			var gDuplicateProjectWizardMode = 0;
			var gDuplicateWorkflowType = 0;
			var isCustomerAuthEnabled = "<?php echo $_smarty_tpl->tpl_vars['customerupdateauthrequired']->value;?>
";
			var eventStack = [];

			window.addEventListener('DOMContentLoaded', function(event) {

			    initialize();

                document.body.onresize = function(e) {
                    resizeApp();
                };

                document.body.addEventListener('click', decoratorListener);
                document.body.addEventListener('keyup', decoratorListener);
                document.body.addEventListener('change', decoratorListener);
            });

        <?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value) {?>
            // wrapper redeemGiftCard
            function fnInputGiftCard(pElement)
            {
                if (pElement.value != '')
                {
                    forceAlphaNumeric(pElement);

                    // Check for enter key. 
                    if (enterKeyPressed(event))
                    {
                        redeemGiftCard();
                    }
                }

                return false;
            }

            // wrapper redeemGiftCard
            function fnRedeemGiftCard(pElement)
            {
                return redeemGiftCard();
            }
        <?php }?>

			// wrapper for logout
			function fnLogout(pElement)
            {
                document.getElementById('headerform').submit();
            }

            // wrapper for changeSystemLanguageSmallScreen
            function fnChangeSystemLanguageSmallScreen(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return changeSystemLanguageSmallScreen(pElement.getAttribute('data-code'));
            }

            // wrapper method for menuAction
            function fnMenuAction(pElement)
            {
                if (!pElement) {
                  return false;
                }

                return menuAction(pElement.getAttribute('data-url'), pElement.getAttribute('data-action'));
            }

            // wrapper method for showPanelAccount
            function fnShowPanelAccount(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return showPanelAccount(JSON.parse(pElement.getAttribute('data-visible')), pElement.getAttribute('data-div-id'));
            }

            // wrapper method for showOrderDetails
            function fnShowOrderDetails(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return showOrderDetails(JSON.parse(pElement.getAttribute('data-show')), pElement.getAttribute('data-product-id'))
            }

            // wrapper method for showPreview
            function fnShowPreview(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return showPreview(pElement.getAttribute('data-url'));
            }

            // wrapper method for
            function fnExecuteButtonAction(pElement)
            {
                if (!pElement) {
                    return false;
                }
                return executeButtonAction(
                    pElement,
                    pElement.getAttribute('data-target'),
                    pElement.getAttribute('data-project-name'),
                    pElement.getAttribute('data-application-name'),
                    pElement.getAttribute('data-project-ref'),
                    pElement.getAttribute('data-workflow-type'),
                    pElement.getAttribute('data-product-indent'),
                    pElement.getAttribute('data-wizard-mode')
                );
            }

            // wrapper for shareByEmail
            function fnShareByEmail(pElement)
            {
                return shareByEmail();
            }

            // wrapper for showShareDetails
            function fnShowShareDetails(pElement)
            {
                if (!pElement) {
                    return false
                }

                return showShareDetails(JSON.parse(pElement.getAttribute('data-show')), pElement.getAttribute('data-action'), JSON.parse(pElement.getAttribute('data-resize')));
            }

            // wrapper for passwordDisplay
            function fnPasswordDisplay(pElement)
            {
                return passwordDisplay();
            }

            // wrapper for verifyAddress
            function fnVerifyAddress(pElement)
            {
				if (isCustomerAuthEnabled)
				{
					return showVerifyPassword(arguments[1]);
				}
				else
				{
					return verifyAddress();
				}
            }

            // wrapper for checkFormChangePassword
            function fnCheckFormChangePassword(pElement)
            {
                return checkFormChangePassword();
            }

            // wrapper for checkFormChangePreferences
            function fnCheckFormChangePreferences(pElement)
            {
                return checkFormChangePreferences();
            }

            // wrapper for showOnlineOptions
            function fnShowOnlineOptions(pElement, pEvent)
            {
                if (!pElement) {
                    return false;
                }

                if (undefined !== pEvent.path) {
                    // Check to see if this is a keep project link click that has triggered this.
                    if (pEvent.path[0].classList.contains('keepProjectLink')) {
                        return false;
                    }
                }

                return showOnlineOptions(JSON.parse(pElement.getAttribute('data-show')), pElement.getAttribute('data-product-id'));
            }

            // wrapper for showOnlineNameForm
            function fnShowOPActionPanel(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return showOPActionPanel(JSON.parse(pElement.getAttribute('data-show')));
            }

            // wrapper for executePayNow
            function fnExecutePayNow(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return executePayNow(pElement.getAttribute('data-session-id'));
            }

            // wrapper for onlineProjectsButtonAction
            function fnOnlineProjectsButtonAction(pElement)
            {
                if (!pElement) {
                  return false;
                }

                return onlineProjectsButtonAction(pElement.getAttribute('data-button'), pElement.getAttribute('data-wizard-mode'), pElement.getAttribute('data-work-type'));
            }

            // wrapper for showOrderPreview
            function fnShowOrderPreview(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return showOrderPreview(JSON.parse(pElement.getAttribute('data-show')), JSON.parse(pElement.getAttribute('data-hide-header')));
            }

            // wrapper for password strength meter
            function fnHandlePasswordStrength(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return passwordStrength.scorePassword(pElement.value, 'strengthvalue', 'strengthtext');
            }

            // wrapper for CJKHalfWidthFullWidthToASCII
            function fnCJKHalfWidthFullWidthToASCII(pElement)
            {
                if (!pElement) {
                    return false;
                }

                return CJKHalfWidthFullWidthToASCII(pElement, JSON.parse(pElement.getAttribute('data-force-uppercase')));
            }

            // wrapper for countryChange
            function fnCountryChange(pElement, pEvent)
            {
                if ((!pElement) || (pEvent.type != 'change')) {
                    return false;
                }

                country = pElement.value;

                processAjaxSmallScreen("addressForm",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&hideconfigfields=" + hideConfigFields +"&strict=1&edit=<?php echo $_smarty_tpl->tpl_vars['edit']->value;?>
&ishighlevel=" + isHighLevel + "&mawebhlbr=" + basketRef , 'GET', '');
            }

			// Listener for hide/show password.
			function fnTogglePreviewPasword(pElment)
			{
				togglePasswordVisibility(pElment, 'previewPassword');
			}

			function fnToggleNewPasword(pElment)
			{
				togglePasswordVisibility(pElment, 'newpassword');
			}

			function fnDeleteOrderLine(pElement, pEvent)
            {
                if (!pElement)
                {
                    return false;
                }

                var confirmDeleteMessageTemplate = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleteProjectConfirmation');?>
";
                var orderNumber = '';
                var eventParams = '';

                if (pEvent.target.classList.contains('deleteOrderButton'))
                {
                    orderNumber = pEvent.target.dataset.ordernumber;
                    eventParams = Object.keys(pEvent.target.dataset).map(function(key) {
                        return key + '=' + encodeURIComponent(pEvent.target.dataset[key]);
                    }).join('&');
                }
                else
                {
                    orderNumber = pEvent.target.parentElement.dataset.ordernumber;
                    eventParams = Object.keys(pEvent.target.parentElement.dataset).map(function(key) {
                        return key + '=' + encodeURIComponent(pEvent.target.parentElement.dataset[key]);
                    }).join('&');
                }

                orderNumber = orderNumber.trim();
                var confirmDeleteMessage = confirmDeleteMessageTemplate.replace('^0', "'" + orderNumber + "'");
                showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDeleteProject');?>
", confirmDeleteMessage, function(e) {
                    processAjax('orderdelete', '?fsaction=Customer.deleteOrder', 'POST', eventParams, false);
                    return true;
                });
            }

            /**
            * initialize
            *
            * initialize the application
            */
            function initialize()
            {
                //prompt the loading dialog
                showLoadingDialog();

                initilializeApp(true);

                onloadWindow();

                //window.onhashchange = locationHashChanged;

                addEventListener("keydown", function (e)
                {
                    if (e.key === 'Enter' || e.keyCode == 13)
                    {
                        // execute specific action compare to the form a user is on
                        switch(window.location.hash)
                        {
                            case "#accountDetails":
                                if (!isCustomerAuthEnabled)
                                {
                                    verifyAddress();
                                }
                                else
                                {
                                    showVerifyPassword(e);
                                }
                                break;
                            case "#changePassword":
                                checkFormChangePassword();
                                break;
                            case '#sharedetail':
                                shareByEmail();
                                break;
                            case '#share':
                                showShareDetails(true, 'email', false);
                                break;
                        }
                    }
                }, false);
            }

            /**
            * locationHashChanged
            *
            * Action executed on back and forward buttons
            */
            function locationHashChanged()
            {
                // action on forward
                if (gPreviousHash == window.location.hash)
                {
                    gActiveAction = gPreviousHash.replace('#', '');
                    showPanelAccount(true, 'contentPanelAjax');
                }
                else
                {
                    // action on back
                    switch(gPreviousHash)
                    {
                        case '#yourorders':
                            if (window.location.hash == '')
                            {
                                showPanelAccount(false, '');
                            }
                            else
                            {
                                if (gHistoryProduct != '')
                                {
                                    showOrderDetails(true, gHistoryProduct);
                                }
                            }
                            break;
                        case '#yourordersdetails':
                            switch(window.location.hash)
                            {
                                case '#share':
                                    showPanelAccount(true, 'contentPanelShare');
                                    break;
                                case '#preview':
                                    break;
                                default:
                                    showOrderDetails(false, '');
                            }
                            break;
                        case '#share':
                            switch(window.location.hash)
                            {
                                case '#sharedetail':
                                    break;
                                case '#yourordersdetails':
                                    //showPanelAccount(false, '');
                                    break;
                                default:
                                    showPanelAccount(false, '');
                            }
                            break;
                        case '#sharedetail':
                            showShareDetails(false, '', false);
                            break;
                        case '#preview':
                            showOrderPreview(false);
                            break;
                    }

                    // don't store hash if we are at the root
                    if (window.location.hash != '')
                    {
                        gPreviousHash = window.location.hash;
                    }
                }
            }

            var a2a_config = {
                num_services: 3000,
                show_menu: {
                    position: "static",
                    top: "0px",
                    left: "0px"
                },
                color_link_text: "333333",
                color_link_text_hover: "333333"
            };

            a2a_config.callbacks = a2a_config.callbacks || [];
			a2a_config.callbacks.push({
                ready: reinitAddtoany,
                share: my_addtoany_onshare
            });

            a2a_config.exclude_services = ["email"];


            function reinitAddtoany()
            {
                // we need to do it two times, one for the tab and one for the link in list
                if (document.getElementById('a2apage_EMAIL'))
                {
                    document.getElementById('a2apage_EMAIL').parentNode.removeChild(document.getElementById('a2apage_EMAIL'));
                }

                if (document.getElementById('a2apage_email'))
                {
                    document.getElementById('a2apage_email').parentNode.removeChild(document.getElementById('a2apage_email'));
                }
            }

            function my_addtoany_onshare(pData)
            {
                if (!checkPassword())
                {
                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
", function(e) {
                        closeDialog(e);
                    });

                    return{
                        stop: true
                    };
                }
                else
                {
					var format = ((document.location.protocol != 'https:') ? 1 : 0);
                    var previewPasswordValue = '';
                    if( document.getElementById('sharepassword').checked)
                    {
                        var previewPasswordObj = document.getElementById('previewPassword');
                        if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                        {
                            previewPasswordValue = (format == 1) ? hex_md5(previewPasswordObj.value) : previewPasswordObj.value;
                        }
                    }
                    showShareDetails(false, '', false);
                    showPanelAccount(false, '', false);

                    if (shareObjLink)
                    {
                        var prevSibling = shareObjLink.previousSibling;
                        while (prevSibling && prevSibling.nodeType != 1)
                        {
                            prevSibling = prevSibling.previousSibling;
                        }
                        prevSibling.style.display = 'block';
                    }

                    var newURl = processAjax("shareurl",".?fsaction=Share.shareAddToAny", 'POST', "orderItemId=" + orderItemID + "&method=" + encodeURIComponent(pData.service) + "&previewPassword=" + encodeURIComponent(previewPasswordValue) + '&format=' + format, false);

                    // due to a bug in add to any we must update the first node on the add to any object with the new project name and share url
                  a2a_config.linkurl = newURl;
                  a2a_config.linkname = shareName;

                  var shareLinks = document.getElementsByClassName('a2a_i');
                  if (shareLinks.length > 0) {
                    for (var i = 0; i < shareLinks.length; i++) {
                      if (pData.service !== shareLinks[i].innerText.trim()) {
                        continue;
                      }
                      if ('/#' + pData.service === shareLinks[i].href) {
                        continue;
                      }

                      var initLink = shareLinks[i].href;
                      var shareUrl = new URL(shareLinks[i].href);
                      shareUrl.searchParams.set('linkurl', newURl);
                      shareUrl.searchParams.set('linkname', shareName);
                      shareLinks[i].setAttribute('href', shareUrl.href);
                    }
                  }

                  setTimeout(function() {
                    return {
                      url: newURl,
                      title: shareName
                    }
                  }, 150);
                }
            }


            /**
            * changeSystemLanguageSmallScreen
            *
            * Set the new language and reload the page
			*
			* @param pLanguageCode string // new language code
            */
            function changeSystemLanguageSmallScreen(pLanguageCode)
            {
                createCookie("maweblocale", pLanguageCode, 24 * 365);
                document.submitformmain.fsaction.value = "Customer.initialize";
                document.submitformmain.submit();
            }

            /**
            * toggleLanguageOption
            *
            * Open or close the language list and apply the correct style compare to the status
            */
            function toggleLanguageOption()
            {
                if (gDialogIsOpen == false)
                {
                    document.getElementById('language-list-popup').style.display = 'block';
                    document.getElementById('img-language-popup').style.backgroundImage="url(<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/toggle_up_grey_v2.png)";
                    gDialogIsOpen = true;
                }
                else
                {
                    document.getElementById('language-list-popup').style.display = 'none';
                    document.getElementById('img-language-popup').style.backgroundImage="url(<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/toggle_down_grey_v2.png)";
                    gDialogIsOpen = false;
                }
            }

            function resizeApplication()
            {
                 // reset the scroll position to force the page to be at the top when displayed
                gScreenWidth = 0;
                document.getElementById('headerSmall').style.display = 'block';

                initilializeApp(false);

                gActiveResizeFormID = new Date().getTime();

                // execute a different compare the the current panel
                switch(gActivePanel)
                {
                    case 'contentPanelRedeemGiftCard':
                        showPanelAccount(true, 'contentPanelRedeemGiftCard');
                        break;

                    case 'contentPanelAjax':

                        switch(gActiveAction)
                        {
                            case 'accountDetails':

                                var container = document.getElementById('contentRightScrollForm');
                                container.scrollTop = 0;
                                contentVisible = container.getElementsByClassName('contentVisible')[0];
                                contentVisible.style.height = 'auto';

                                if (gAddressupdated != "0")
                                {
                                    setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');
                                }
                                else
                                {
                                    setScrollAreaHeight('contentRightScrollForm', '');
                                }

                                showPanelAccount(true, 'contentPanelAjax');
                                break;
                            default:
                                setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');

                                initializePanel();

                                closeLoadingDialog();

                                showPanelAccount(true, 'contentPanelAjax');

                                if (gIsPreviewActive)
                                {
                                    if (gIsPreviewValid)
                                    {
										showLoadingDialog();

                                        resetPreview();

                                        initPreview(false);

                                        //move to the current image
                                        showCurrent(gCurrentPosition);
                                        showOrderPreview(true, true);
                                    }
                                    else
                                    {
                                        showOrderPreview(true, false);
                                    }
                                }

                                if (gActiveProduct != '')
                                {
                                    showOrderDetails(true, gActiveProduct);
                                }

                                if (gActiveProductOnline != '')
                                {
                                    showOnlineOptions(true, gActiveProductOnline);

                                    if (gNameForm)
                                    {
                                        document.getElementById('onlineDetailPanel').style.marginLeft = '-' + gScreenWidth + 'px';
                                        setScrollAreaHeight('contentRightScrollNameForm', 'contentNavigationNameForm');
                                    }
                                }
                        }
                        break;

                    case 'contentPanelShare':

                        setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');

                        initializePanel();

                        closeLoadingDialog();

                        showPanelAccount(true, 'contentPanelAjax');

                        if (gIsPreviewActive)
                        {
                            if (gIsPreviewValid)
                            {
								showLoadingDialog();

                                resetPreview();

                                initPreview(false);

                                //move to the current image
                                showCurrent(gCurrentPosition);
                                showOrderPreview(true, true);
                            }
                            else
                            {
                                showOrderPreview(true, false);
                            }
                        }

                        if (gActiveProduct != '')
                        {
                            document.getElementById('orderMainPanel').style.marginLeft = '-' + gScreenWidth + 'px';
                        }

                        showPanelAccount(true, 'contentPanelShare');

                        if (gShareDetailActive != '')
                        {
                            showShareDetails(true, gShareDetailActive, true);
                        }
                        break;
                }

                if (gDialogStatus == 'open')
                {
                    setDialogPosition();
                }
            }

            function onKeyboardAction()
            {
                 switch(gActivePanel)
                {
                    case 'contentPanelRedeemGiftCard':
                        setContentVisibleHeight('contentPanelRedeemGiftCardForm');
                    break;

                    case 'contentPanelAjax':
                        setContentVisibleHeight('contentRightScrollForm');
                    break;

                    case 'contentPanelShare':
                        if (gShareDetailActive != '')
                        {
                            setContentVisibleHeight('contentRightScrollShareDetail');
                        }
                        else
                        {
                            if (gActiveProductOnline != '')
                            {
                                if (gNameForm)
                                {
                                    setContentVisibleHeight('contentRightScrollNameForm');
                                }
                                else
                                {
                                    setContentVisibleHeight('contentRightScrollShare');
                                }
                            }
                            else
                            {
                                setContentVisibleHeight('contentRightScrollShare');
                            }
                        }
                    break;
                }
            }

            /**
            * processAjaxSmallScreen
            *
            * send an ajax query to the server and get the respond back
			*
			* @param obj string // action name
			* @param serverPage
			* @param requestMethod string // get or post
			* @param params array
            */
           function processAjaxSmallScreen(obj, serverPage, requestMethod, params)
           {
                // add the ref and ssotoken onto the URL if it is missing
                if ((serverPage.indexOf('&ref=') == -1) || (serverPage.indexOf('?ref=') == -1))
                {
                    if (serverPage.indexOf('?') != -1)
                    {
                        serverPage += '&ref=' + gSession;
                    }
                    else
                    {
                        serverPage += '?ref=' + gSession;
                    }
                }

                if ((serverPage.indexOf('&ssotoken=') == -1) || (serverPage.indexOf('?ssotoken=') == -1))
                {
                    if (serverPage.indexOf('?') != -1)
                    {
                        serverPage += '&ssotoken=' + gSSOToken;
                    }
                    else
                    {
                        serverPage += '?ssotoken=' + gSSOToken;
                    }
                }

                if ('POST' === requestMethod) {
                       // Add CSRF token to post submissions
                    var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
                    if (csrfMeta) 
                    {
                        var csrfToken = csrfMeta.getAttribute('content');

                        if (typeof params !== 'undefined' && null !== params && params.length > 0)
                        {
                            // only add the token if it is missing
                            if (params.indexOf("csrf_token=") == -1)
                            {
                                params += '&csrf_token=' + csrfToken;
                            }
                        } 
                        else 
                        {
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
                            case 'verifyPassword':
                                try {
                                    var response = JSON.parse(xmlhttp.responseText);
                                    if (response.valid)
                                    {
                                        verifyAddress();
                                        eventStack = [];
                                    }
                                    else
                                    {
                                        showVerificationFailedMessageSmallScreen(response.result, function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                    break;
                                } catch (e) {
                                    showVerificationFailedMessageSmallScreen("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorLoginHasExpired');?>
", handleSessionExpirySmallScreen);
                                    break;
                                }
                            case 'addressForm':
                                //load the content of the template
                                var formWrapper = '<form id="mainform" name="mainform" action="#">' + xmlhttp.responseText + '</form>';
                                document.getElementById('ajaxdiv').innerHTML = formWrapper;

                                restoreFields();

                                

                                <?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>

                                    

                                var as_city = {
                                    script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
                                    varname:"&input",
                                    cache:false,
                                    offsety:0,
                                    json:true,
                                    shownoresults:false,
                                    maxresults:20
                                };

                                var as_county = {
                                    script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
                                    varname:"&input",
                                    cache:false,
                                    offsety:0,
                                    json:true,
                                    shownoresults:false,
                                    maxresults:20
                                };

                                var as_state = {
                                    script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=state&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
                                    varname:"&input",
                                    cache:false,
                                    offsety:0,
                                    json:true,
                                    shownoresults:false,
                                    maxresults:20
                                };

                                gAs_jsonCity = new bsn.AutoSuggest('maincity', as_city, "content");
                                gAs_jsonCounty = new bsn.AutoSuggest('maincounty', as_county, "content");
                                gAs_jsonState = new bsn.AutoSuggest('mainstate', as_state, "content");

                                    

                                <?php }?>

                                

                                if (gAddressupdated != "0")
                                {
                                    setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');
                                }
                                else
                                {
                                    setScrollAreaHeight('contentRightScrollForm', '');
                                }

                                closeLoadingDialog();

                                showPanelAccount(true, 'contentPanelAjax');
                                break;
                            case 'renameonlineproject':
                                try
                                {
                                    var jsonObject = parseJson(xmlhttp.responseText);

                                    if (jsonObject.error == '')
                                    {

                                        if (jsonObject.maintenancemode)
                                        {
                                            closeLoadingDialog();
                                            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
", function(e) {
                                                closeDialog(e);
                                            });

                                        }
                                        else
                                        {

                                            var projectDetails = jsonObject.projectdetails;
                                            if (projectDetails.projectexists == false)
                                            {
                                                showOPActionPanel(false);
                                                removeDeletedProject(projectDetails.projectref, true);
                                            }
                                            else if (projectDetails.nameexists != '')
                                            {
                                                closeLoadingDialog();
                                                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", projectDetails.nameexists, function(e) {
                                                    closeDialog(e);
                                                });
                                            }
                                            else
                                            {

                                                closeLoadingDialog();
                                                var projectName = projectDetails.projectname;
                                                document.getElementById('orderProductLabel' + gActiveProductOnline).innerHTML = projectName;
                                                document.getElementById('pageLabel' + gActiveProductOnline).innerHTML = projectName;

                                                var projectdiv = document.getElementById('onlineProjectDetail' + gActiveProductOnline);
                                                projectdiv.setAttribute("data-projectname", projectName);

                                                showOPActionPanel(false);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        window.location.replace(window.location.href.replace(window.location.hash, ''));
                                    }
                                }
                                catch (e)
                                {
                                    window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'getshareonlineprojecturl':
                                try
                                {
                                    var jsonObject = JSON.parse(xmlhttp.responseText);
                                    var shareURL = jsonObject.shareurl;
                                    closeLoadingDialog();
                                    gNameFormAction = 'share';

                                    // Function included in main.tpl
                                    showOPActionPanel(true, {
                                        panel: 'share',
                                        title: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonShareProject');?>
',
                                        label: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareLink');?>
',
                                        labelBtn: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCopyLink');?>
',
                                        value: shareURL,
                                        fn: function(){
                                            // Below two functions are already included in main.tpl
                                            copyValueToClipboard('projectname');
                                            flashTooltip('sharelink-tip','tip-popout-visible');
                                        }
                                   });
                                }
                                catch (e)
                                {
                                    window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'duplicateonlineproject':
                                try
                                {
                                    var duplicateResult = parseJson(xmlhttp.responseText);

                                    var error = duplicateResult.error;
                                    var resultMessage = duplicateResult.error;
                                    var nameExistsError = duplicateResult.nameexists;
                                    var projectExists = duplicateResult.projectexists;

                                    if (isHighLevel)
                                    {
                                        resultMessage = duplicateResult.resultmessage;
                                        nameExistsError = resultMessage;
                                        projectExists = (duplicateResult.result != 5) ? true : false;
                                        error = '';
                                    }

                                    if (error == '')
                                    {
                                        if (duplicateResult.maintenancemode)
                                        {
                                            closeLoadingDialog();
                                            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
", function(e) {
                                                closeDialog(e);
                                            });
                                        }
                                        else
                                        {
                                            if (projectExists == false)
                                            {
                                                showOPActionPanel(false)
                                                removeDeletedProject(duplicateResult.projectref, true);
                                            }
                                            else if (nameExistsError != '')
                                            {
                                                closeLoadingDialog();
                                                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", nameExistsError, function(e) {
                                                    closeDialog(e);
                                                });
                                            }
                                            else
                                            {
                                                duplicateOnlineProjectCallBack(duplicateResult);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        window.location.replace(window.location.href.replace(window.location.hash, ''));
                                    }
                                }
                                catch (e)
                                {
                                    window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'checkDeleteSessioncontinueediting':
                            case 'checkDeleteSessionediting':
                                var response = parseJson(xmlhttp.responseText);

                                var editingType = 0;

                                if (obj == 'checkDeleteSessioncontinueediting');
                                {
                                    editingType = 1;
                                }

                                if (response.error == '')
                                {
                                    if (response.maintenancemode)
                                    {
                                        closeLoadingDialog();
                                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
", function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                    else
                                    {
                                        var targetProjectRef = {};
                                        for (var key in response.projectitemarray)
                                        {
                                            targetProjectRef = response.projectitemarray[key];
                                        }

                                        if (targetProjectRef.canmodify == 0)
                                        {
                                            changeCanModify(targetProjectRef.projectref);
                                        }
                                        else
                                        {
                                            if (targetProjectRef.sessionactive == true)
                                            {
                                                displayTerminateSessionConfirmation(targetProjectRef.sessiontype, 'editing');
                                            }
                                            else
                                            {
                                                if (targetProjectRef.projectexists == true)
                                                {
                                                    openExistingOnlineProject(editingType);
                                                }
                                                else
                                                {
                                                    removeDeletedProject(targetProjectRef.projectref, true);
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;

                            case 'checkDeleteSessioncomplete':
                                var response = parseJson(xmlhttp.responseText);
                                if (response.error == '')
                                {
                                    if (response.maintenancemode)
                                    {
                                        closeLoadingDialog();
                                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
", function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                    else
                                    {
                                        var targetProjectRef = {};
                                        for (var key in response.projectitemarray)
                                        {
                                            targetProjectRef = response.projectitemarray[key];
                                        }

                                        if (targetProjectRef.canmodify == 0)
                                        {
                                            changeCanModify(targetProjectRef.projectref);
                                        }
                                        else
                                        {
                                            if (targetProjectRef.sessionactive == true)
                                            {
                                                displayTerminateSessionConfirmation(targetProjectRef.sessiontype, 'complete');
                                            }
                                            else
                                            {
                                                if (targetProjectRef.projectexists == true)
                                                {
                                                    completeOrder();
                                                }
                                                else
                                                {
                                                    removeDeletedProject(targetProjectRef.projectref, true);
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'checkDeleteSessiondelete':
                                var response = parseJson(xmlhttp.responseText);
                                if (response.error == '')
                                {
                                    if (response.maintenancemode)
                                    {
                                        closeLoadingDialog();
                                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
", function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                    else
                                    {
                                        var targetProjectRef = {};
                                        for (var key in response.projectitemarray)
                                        {
                                            targetProjectRef = response.projectitemarray[key];
                                        }

                                        if (targetProjectRef.canmodify == 0)
                                        {
                                            changeCanModify(targetProjectRef.projectref);
                                        }
                                        else
                                        {
                                            if (targetProjectRef.sessionactive == true)
                                            {
                                                displayTerminateSessionConfirmation(targetProjectRef.sessiontype, 'delete');
                                            }
                                            else
                                            {
                                                if (targetProjectRef.projectexists == true)
                                                {
                                                    removeDeletedProject(targetProjectRef.projectref, false);
                                                }
                                                else
                                                {
                                                    removeDeletedProject(targetProjectRef.projectref, true);
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'openonlineproject':

                                var response = parseJson(xmlhttp.responseText);

                                var resultMessage = '';
                                var redirectURL = '';

                                if (isHighLevel)
                                {
                                    resultMessage = response.resultmessage;
                                    redirectURL = response.designurl;
                                }
                                else
                                {
                                    resultMessage = response.errorparam;
                                    redirectURL = response.brandurl;
                                }

                                if (resultMessage == '')
                                {
                                    window.location.href =  redirectURL;
                                }
                                else
                                {
                                    if (resultMessage == 'str_ErrorSessionExpired')
                                    {
                                        window.location.replace(window.location.href.replace(window.location.hash, ''));
                                    }
                                    else
                                    {
                                        closeLoadingDialog();
                                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", resultMessage, function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                }

                                break;
                            case 'completeorder':
                                var response = parseJson(xmlhttp.responseText);

                                if (response.errorparam == '')
                                {
                                    window.location.href =  response.brandurl;
                                }
                                else
                                {
                                    if (resultMessage == 'str_ErrorSessionExpired')
                                    {
                                        window.location.replace(window.location.href.replace(window.location.hash, ''));
                                    }
                                    else
                                    {
                                        closeLoadingDialog();
                                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", response.errorparam, function(e) {
                                            closeDialog(e);
                                        });
                                    }
                                }

                                break;
                            case 'shareByEmail':
                                try
                                {
                                   var shareResult = parseJson(xmlhttp.responseText);
                                   var confirmationBoxTextObj = document.getElementById('dialogContent');
                                   if (shareResult)
                                   {
                                       if (shareResult['result'] == '')
                                       {
                                           confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
                                           confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailSent');?>
";

                                           if (shareObjLink)
                                           {
                                               var prevSibling = shareObjLink.previousSibling;

                                               while (prevSibling && (prevSibling.nodeType != 1))
                                               {
                                                   prevSibling = prevSibling.previousSibling;
                                               }

                                               if (prevSibling)
                                               {
                                                   prevSibling.style.display = 'block';
                                               }
                                           }
                                       }
                                       else
                                       {
                                           confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
                                       }
                                   }
                                   else
                                   {
                                       confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
                                   }

                                   document.getElementById('dialogBtn').style.display = 'block';
                                }
                                catch(e)
                                {
                                   window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'mailToLink':
                                try
                                {
                                   var shareResult = parseJson(xmlhttp.responseText);
                                   var confirmationBoxTextObj = document.getElementById('dialogContent');
                                   if (shareResult)
                                   {
                                       if (shareResult['result'] == '')
                                       {
                                           confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
                                           confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailSent');?>
";

                                           if (shareObjLink)
                                           {
                                               var prevSibling = shareObjLink.previousSibling;

                                               while(prevSibling && (prevSibling.nodeType != 1))
                                               {
                                                   prevSibling = prevSibling.previousSibling;
                                               }

                                               if (prevSibling)
                                               {
                                                   prevSibling.style.display = 'block';
                                               }
                                           }

                                           window.location.href = shareResult['resultparam'];
                                       }
                                       else
                                       {
                                           confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
                                       }
                                   }
                                   else
                                   {
                                       confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
                                   }
                                   document.getElementById('dialogBtn').style.display = 'block';
                                }
                                catch(e)
                                {
                                   window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'unshare':
                                try
                                {
                                   var unshareResult = parseJson(xmlhttp.responseText);
                                   var message = '';
                                   if (unshareResult['success'])
                                   {
                                       if (unshareResult['title'] == '')
                                       {
                                           message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SuccessUnshare');?>
";
                                           if (unshareObjLink)
                                           {
                                               unshareObjLink.style.display = 'none';
                                           }
                                       }
                                       else
                                       {
                                           message = unshareResult['title'] + '. ' + unshareResult['msg'];
                                       }
                                   }
                                   else
                                   {
                                       message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
                                   }

                                   createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, function(e) {
                                        closeDialog(e);
                                   });
                                }
                                catch(e)
                                {
                                   window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'redeemgiftcard':
                                try
                                {
                                	var jsonObj = parseJson(xmlhttp.responseText);

                                	//update the gift card balance
                                	document.getElementById('giftcardbalance').innerHTML = jsonObj.giftcardbalance;

                                	// reset teh input text
                                	var giftCardInput = document.getElementById('giftcardid');
                                	giftCardInput.value = "";
                                	giftCardInput.className = 'inputGiftCard';

                                	// close teh loding dialog
                                	closeLoadingDialog();

                                	// display a message if needed
                                	if (jsonObj.showgiftcardmessage == 1)
                                	{
                                		displayGiftCardAlert(jsonObj.giftcardresult, "");
                                	}

                                	if (jsonObj.giftcardresult == 'str_LabelGiftCardAccepted')
                                	{
                                		showLoadingNotificationBar('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardAccepted');?>
');
                                		showPanelAccount(false, '');
                                	}
                                }
                                catch (e)
                                {
                                	window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'menuAjaxAction':
                                try
                                {
                                	var jsonObj = parseJson(xmlhttp.responseText);

                                	//update the gift card balance
                                	var container = document.getElementById('contentPanelAjax');
                                	container.innerHTML = jsonObj.template;
                                	container.style.display= 'block';

                                	// change javascript
                                	toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                	switch(gActiveAction)
                                	{
                                		case 'accountDetails':
                                			initializePanel();
                                			break;
                                		default:
                                			setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');

                                			initializePanel();

                                			closeLoadingDialog();

                                			showPanelAccount(true, 'contentPanelAjax');
                                	}
                                }
                                catch (e)
                                {
                                	window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                            case 'updateAction':
                                try
                                {
                                	var jsonObj = parseJson(xmlhttp.responseText);

                                	//display confirmation message
                                	if ((jsonObj.message != '') && (jsonObj.section != 'menu'))
                                	{
                                		createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", jsonObj.message, function(e) {
                                		    closeDialog(e);
                                        });
                                	}

                                	// close the loading dialog
                                	closeLoadingDialog();

                                	gAddressupdated = "1";

                                	if (jsonObj.section == 'menu')
                                	{
                                		if ((jsonObj.message != '') && (jsonObj.section == 'menu'))
                                		{
                                			showLoadingNotificationBar(jsonObj.message);
                                		}
                                		showPanelAccount(false, '');

                                		// set the location hash to # to prevent listeners loading on homepage
                                        window.location.replace(window.location.href.replace(window.location.hash, '#'));
                                	}
                                }
                                catch (e)
                                {
                                	window.location.replace(window.location.href.replace(window.location.hash, ''));
                                }
                                break;
                        }
                    }
                    else if ((xmlhttp.readyState == 4) && ((xmlhttp.status == 405) || (xmlhttp.status == 403)))
                    {
                        handleSecurityError(xmlhttp.responseText);
                    }
                };

                if (requestMethod == 'GET')
                {
                    xmlhttp.send(null);
                }
                else if (requestMethod == 'POST')
                {
                    xmlhttp.send(params);
                }

           }

            /**
            * showPanelAccount
            *
            * slide from a panel to another panel
			*
			* @param pVisible boolean // true if the action is to show a new panel
			* @param pDivID string // ID of the container
            */
            function showPanelAccount(pVisible, pDivID)
            {
                gActiveResizeFormID = new Date().getTime();

                // main panel
                if (pVisible)
                {
                    // force the panel to be visible
                    document.getElementById(pDivID).style.display= 'block';

                    // slide to the panel
                    switch(pDivID)
                    {
                        case 'contentPanelRedeemGiftCard':

                            setScrollAreaHeight('contentPanelRedeemGiftCardForm', 'contentPanelRedeemGiftCardNavigation');

                            //hide the ajax panel
                            document.getElementById('contentPanelAjax').style.display= 'none';

                            // slide to the right panel
                            document.getElementById('contentPanelMain').style.marginLeft = '-' + gScreenWidth + 'px';

                            window.location.hash = "giftcard";

                            break;
                        case 'contentPanelAjax':
                            //hide the ajax panel
                            document.getElementById('contentPanelRedeemGiftCard').style.display= 'none';

                            // slide to the right panel
                            document.getElementById('contentPanelMain').style.marginLeft = '-' + gScreenWidth + 'px';
                            break;
                        case 'contentPanelShare':
                            // slide back to the left panel
                            if (gIsPreviewActive)
                            {
                                document.getElementById('headerSmall').style.display = 'block';
                            }

                            initializeSharePanel();

                            document.getElementById('contentPanelAjax').style.width = gScreenWidth + 'px';
                            document.getElementById('contentPanelAjax').style.marginLeft = '-' + gScreenWidth + 'px';
                            break;
                    }

                   gActivePanel = pDivID;
                }
                else
                {
                    // slide back to a panel
                    switch(gActivePanel)
                    {
                        case 'contentPanelRedeemGiftCard':
                        case 'contentPanelAjax':
                            // slide back to the left panel
                            document.getElementById('contentPanelMain').style.marginLeft = 0;

                            setScrollAreaHeight('contentLeftScrollMenu', '');

							gActiveAction = '';

                            window.location.hash = '';

                            // delay to hide the panel to make sure the other panel is displayed (css animation)
                            setTimeout(function()
                            {
                                // hide the previous panel
                                document.getElementById(gActivePanel).style.display= 'none';
                                gActivePanel = '';
                            }, 300);
                            break;

                        case 'contentPanelShare':
                            // slide back to the left panel
                            if (gIsPreviewActive)
                            {
                                document.getElementById('headerSmall').style.display = 'none';
                            }

                            if (gActiveProduct != '')
                            {
                                setScrollAreaHeight('contentRightScrollDetail', 'contentNavigationDetail');
                            }

                            document.getElementById('passwordForm').style.display = 'none';
                            document.getElementById('contentPanelAjax').style.width = (gScreenWidth * 2) + 'px';
                            document.getElementById('contentPanelAjax').style.marginLeft = 0;
                            gActivePanel = 'contentPanelAjax';

                            window.location.hash = 'yourordersdetails';

                            break;
                    }
                }
            }

            function showOrderDetails(pShow, pProductID)
            {
                gActiveResizeFormID = new Date().getTime();
                if (pShow)
                {
                    document.getElementById('productDetail' + pProductID).style.display = 'block';
                    setScrollAreaHeight('contentRightScrollDetail', 'contentNavigationDetail');
                    document.getElementById('orderMainPanel').style.marginLeft = '-' + gScreenWidth + 'px';
                    gActiveProduct = pProductID;

                    window.location.hash = "yourordersdetails";
                }
                else
                {
                    document.getElementById('orderMainPanel').style.marginLeft = 0;
                    // delay to hide the panel to make sure the other panel is displayed (css animation)
                    setTimeout(function()
                    {
                        document.getElementById('productDetail' + gActiveProduct).style.display = 'none';
                        gHistoryProduct = gActiveProduct;
                        gActiveProduct = '';

                        window.location.hash = "yourorders";
                    }, 300);
                }
            }

            function showShareDetails(pShow, pAction, pResize)
            {
                gActiveResizeFormID = new Date().getTime();

                if (pShow)
                {
                    var messageError = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
                    if (!checkPassword())
                    {
                        messageError +="<br /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
";
                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", messageError, function(e) {
                            closeDialog(e);
                        });
                    }
                    else
                    {
                        if (pAction == "email")
                        {
                            if (pResize == false)
                            {
                                var emailTitle = document.getElementById('shareByEmailTitle');
                                if (emailTitle)
                                {
                                    emailTitle.value = '';
                                }

                                var emailTo = document.getElementById('shareByEmailTo');
                                if (emailTo)
                                {
                                    emailTo.value = '';
                                }

                                var emailText = document.getElementById('shareByEmailText');
                                if (emailText)
                                {
                                    emailText.value = '';
                                }
                            }

                            document.getElementById('shareSocial').style.display = 'none';
                            document.getElementById('shareEmail').style.display = 'block';
                            window.location.hash = "sharedetail";
                        }
                        else
                        {
                            document.getElementById('shareEmail').style.display = 'none';
                            document.getElementById('shareSocial').style.display = 'block';
                            initializeShareSocial();
                            window.location.hash = "sharedetail";
                        }

                        document.getElementById('contentPanelShareDetail').style.display = 'block';
                        document.getElementById('contentRightScrollShareDetail').scrollTop = 0;

                        setScrollAreaHeight('contentRightScrollShareDetail', 'contentNavigationShareDetail');
                        document.getElementById('contentPanelShare').style.marginLeft = '-' + gScreenWidth + 'px';
                       
                        // Fix an issue with IOS not repainting the cscreen has expected.
                        // Time is set to 350 becuse the animation is taking 300 ms to be completed.
                        setTimeout(function(){
                            document.getElementById('contentPanelShare').style.webkitTransform = 'scale(1)';
                        }, 350);
                       
                        gShareDetailActive = pAction;
                    }
                }
                else
                {
                    document.getElementById('contentPanelShare').style.marginLeft = 0;
                    setTimeout(function()
                    {
                        document.getElementById('contentPanelShareDetail').style.display = 'none';
                        gShareDetailActive = '';
                    }, 300);
                }
            }

            function showPreview(pAddress)
            {
                showLoadingDialog();

				// use JSONP
				toggleJs('previewlink', '', true, encodeURI(pAddress) + '&callback=showPreviewCallBack', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
', '', undefined, true);

                window.location.hash = "preview";
            }

			function showPreviewCallBack(pJsonP)
			{
				try
				{
					var jsonObj = pJsonP;

					//load the content of the template
					document.getElementById('orderPreviewPanel').innerHTML = jsonObj.template;

					if (jsonObj.javascript != '')
					{
						// check if we need to load extra css for page flip
						if (jsonObj.css != '')
						{
							// load pageflip CSS
							addCSS(jsonObj.css);
						}
                        
                        var contentCustomScript = document.getElementById('externalscript1');

						// check if we need to load additionnal scripts for pageflip
						if ((jsonObj.scripturl1 != '') && (jsonObj.scripturl2 != '') && (! contentCustomScript))
						{
							// remove JS comments
							var mainScript = jsonObj.javascript.replace(/^.*\/\/ .*$/mg, '');

							// protect single quote
							mainScript = mainScript.replace(/'/g, '\\\'');

							// remove carriage return
							mainScript = mainScript.replace(/[\n\r]+/g, ' ');

							// load additionnal scripts and the main script
							var callBackScript = 'toggleJs("externalscript1", "", true, "' + jsonObj.scripturl2 + '", "<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
", \'toggleJs("mainjavascript", \"' + mainScript + '\", true, "", "<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
"); initPreview(true); showOrderPreview(true, true);\');';
							toggleJs('externalscript', '', true, jsonObj.scripturl1, '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
', callBackScript);
						}
						else
						{
							// copy javascript content
							toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

							initPreview(true);

							showOrderPreview(true, true);
						}
					}
				}
				catch (e)
				{
					window.location.replace(window.location.href.replace(window.location.hash, ''));
				}
			}

            function showOrderPreview(pShow, pHideHeader)
            {
                if (pShow)
                {
                    gIsPreviewValid = pHideHeader;
                    if (pHideHeader)
                    {
                        document.getElementById('headerSmall').style.display = 'none';
                    }
                    document.getElementById('orderDetailPanel').style.marginLeft = '-' + gScreenWidth + 'px';
                    gIsPreviewActive = true;
                }
                else
                {
					// Remove page flip when closing the preview if page flip preview is used
					if (document.getElementById('pf-pagerin'))
					{
						$('#pageflip').pageflip().closePageflip(function() {});
					}

                    document.getElementById('headerSmall').style.display = 'block';
                    if (gActiveProduct != '')
                    {
                        setScrollAreaHeight('contentRightScrollDetail', 'contentNavigationDetail');
                    }
                    document.getElementById('orderDetailPanel').style.marginLeft = 0;
					setTimeout(function()
                    {
						document.getElementById('orderPreviewPanel').innerHTML = '';
						gIsPreviewActive = false;
					}, 300);
                }
            }

            function showOnlineOptions(pShow, pProductID)
            {
                if (pShow)
                {
                    document.getElementById('onlineProjectDetail' + pProductID).style.display = 'block';
                    setScrollAreaHeight('contentRightScrollDetail', 'contentNavigationDetail');
                    document.getElementById('onlineMainPanel').style.marginLeft = '-' + gScreenWidth + 'px';
                    gActiveProductOnline = pProductID;
                }
                else
                {
                    document.getElementById('onlineMainPanel').style.marginLeft = 0;
                    // delay to hide the panel to make sure the other panel is displayed (css animation)
                    setTimeout(function()
                    {
                        document.getElementById('onlineProjectDetail' + gActiveProductOnline).style.display = 'none';
                        gActiveProductOnline = '';
                    }, 300);
                }
            }

            function verifyAccountSmallScreen()
            {
                var passwordObj = document.getElementById('password');
                if (passwordObj)
                {
                    var value = passwordObj.value;
                    if (value.length)
                    {
                        var format = ((document.location.protocol != 'https:') ? 1 : 0);
                        var postParams = 'password=' + ((format == 0) ? value : hex_md5(value)) + '&format=' + format;
                        processAjaxSmallScreen("verifyPassword", ".?fsaction=Customer.verifyPassword", 'POST', postParams, true);
						window['confirmValue'] = value;
						closeDialog();
                    }
                    else
                    {
                        closeDialog();
                        showVerificationFailedMessageSmallScreen("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPassword');?>
", function(e) {
                            closeDialog(e);
                        });
                    }
                }
            }

            function handleSessionExpirySmallScreen()
            {
                var url = '';
                if (window.location.hash == '')
                {
                    url = window.location.href.replace('#', '');
                }
                else
                {
                    url = window.location.href.replace(window.location.hash, '');
                }

                window.location.replace(url);
            }

            function showVerificationFailedMessageSmallScreen(message, callback)
            {
                if(eventStack.length)
                {
                    eventStack = [];
                }
                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", "<p>" + message + "</p>", callback);
            }

            function showVerifyPassword(event)
            {
                if (!eventStack.length)
                {
                    eventStack.push(event);

                    var container = document.createElement('div');
					container.style.position = 'relative';

                    var formLine1 = document.createElement('div');
                    formLine1.classList = 'formLine1';

                    var label = document.createElement('label');
                    label.setAttribute('for', 'password');
                    label.innerText = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRenterPassword');?>
:";

                    formLine1.appendChild(label);
                    container.appendChild(formLine1);

                    var formLine2 = document.createElement('div');
                    formLine2.classList = 'formLine2';

                    var wrapper = document.createElement('div');
                    wrapper.classList = 'password-input-wrap';

                    var input = document.createElement('input');
                    input.setAttribute('id', 'password');
                    input.setAttribute('type', 'password');
                    input.setAttribute('name', 'password');
                    input.classList = 'middle dialog-input';

					var button = document.createElement('button');
					button.className = 'password-visibility password-show dialog-password-visibility';
					button.setAttribute('id', 'togglepassword');

					wrapper.appendChild(input);
                    wrapper.appendChild(button);
                    formLine2.appendChild(wrapper);
                    container.appendChild(formLine2);

                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmChanges');?>
", container.outerHTML, verifyAccountSmallScreen);

                    setTimeout(function() {
                        // bring the password input into focus
                        document.getElementById('password').focus();

						// Add listener for the hide/show password.
						var togglePasswordElement = document.getElementById('togglepassword');
						if (togglePasswordElement)
						{
							togglePasswordElement.addEventListener('click', function()
							{
								togglePasswordVisibility(togglePasswordElement, 'password');
							});
						}
                    }, 300);
                }
                else
                {
                    eventStack = [];
                    return verifyAccountSmallScreen();
                }
            }

            

         //]]>
        <?php echo '</script'; ?>
>
    </head>
    <body>

         <!-- DIALOGS -->

        <div id="shim" class="shim">&nbsp;</div>
        <div id="shimSpinner" class="shimSpinner">&nbsp;</div>

        <div id="dialogOuter" class="dialogOuter"></div>

        <div id="dialogLoading" class="dialogLoading">
          <img class="loadingImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/mobile_loading.png" alt=""/>
        </div>

        <!-- END DIALOGS -->

        <!-- HIDDEN DIV TO ACCESS STYLE -->

        <div id="contentScrollCart" class="contentScrollCart hide"></div>
        <div id="outerBox" class="outerBox hide"></div>
        <div id="outerBoxPadding" class="outerBoxPadding hide"></div>
        <div id="dialogContentHide" class="dialogContent hide"></div>
        <input type="text" id="inputText" class="hide" />
        <div id="dropDownGeneric" class="wizard-dropdown hide"></div>

        <!-- END HIDDEN DIV TO ACCESS STYLE -->

        <div id="outerPage" class="outerPage">

            <div id="headerSmall" class="header">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>

            <div class="tpxPopupOptionCustomer" id="language-list-popup">
                <div class="tpx-popup-option-bloc">
                    <div class="tpx-popup-option-content">
                        <?php echo $_smarty_tpl->tpl_vars['systemlanguagelist']->value;?>

                    </div>
                </div>
            </div>

            <div class="notificationBar" id="notificationBar">
                <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/notifications-confirm.png" alt="" />
                <span id="notificationText"></span>
            </div>

            <div class="alertBar" id="alertBar">
                <span id="alertText"></span>
            </div>

            <div id="contentBlocSite" class="contentBlocSite">

                <div id="loadingGif" class="loadingGif"></div>

                <div id="contentPanelMain" class="contentLeftPanel">

                    <div id="contentLeftScrollMenu" class="contentScrollCart">

                        <div class="contentVisible">

                            <?php if ($_smarty_tpl->tpl_vars['hasflaggedonlineprojects']->value) {?>
                                <div id="flaggedForPurge" class="warning-bar"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageMainPurgeWarning');?>
 <a href="#" data-decorator="fnVisitCheckProjects" data-link="<?php echo $_smarty_tpl->tpl_vars['checkprojectslink']->value;?>
" data-internal="<?php echo $_smarty_tpl->tpl_vars['hasonlinedesignerurl']->value;?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePurgeWarningLink');?>
</a></div>
                            <?php }?>

                            <div class="pageLabel">

                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>


                            </div> <!-- pageLabel -->

                            <div id="contentHolder">

                                <div class="">

                                    <div class="content contentMenu" id="content">

                                        <div class="outerBox outerBoxPadding">

                                            <div class="menuItem menuItemYourOrder" data-decorator="fnMenuAction" data-url="Customer.yourOrders" data-action="yourorders">
                                                <img class="iconAction" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/account/icon-your-orders.png" alt="" /><br />
                                                <div class="menuText">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleYourOrders');?>

                                                </div>
                                                <img class="nextImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/account/account-panel-arrow_v2.png" alt=">" />
                                                <div class="clear"></div>
                                            </div>

        <?php if ($_smarty_tpl->tpl_vars['hasonlinedesignerurl']->value == 1) {?>
                                            <div class="menuItem menuItemOnlineProject" data-decorator="fnMenuAction" data-url="Customer.displayOnlineProjectList" data-action="onlineProject">
                                                <img class="iconAction" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/account/icon-online-projects.png" alt=""><br>
                                                <div class="menuText">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleOnlineProjects');?>

                                                </div>
                                                <img class="nextImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/account/account-panel-arrow_v2.png" alt=">" />
                                                <div class="clear"></div>
                                            </div>

        <?php }?> 
        <?php if ($_smarty_tpl->tpl_vars['canmodifyaccountdetails']->value == 1) {?>

                                            <div class="menuItem menuItemAccountDetails" data-decorator="fnMenuAction" data-url="Customer.accountDetails" data-action="accountDetails">
                                                <img class="iconAction" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/account/icon-account-details.png" alt="" /><br />
                                                <div class="menuText">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleAccountDetails');?>

                                                </div>
                                                <img class="nextImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/account/account-panel-arrow_v2.png" alt=">" />
                                                <div class="clear"></div>
                                            </div>

        <?php }?> 
        <?php if ($_smarty_tpl->tpl_vars['canmodifypassword']->value == 1) {?>

                                            <div class="menuItem menuItemChangePassword" data-decorator="fnMenuAction" data-url="Customer.changePassword" data-action="changePassword">
                                                <img class="iconAction" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/account/icon-change-password.png" alt="" /><br />
                                                <div class="menuText">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePassword');?>

                                                </div>
                                                <img class="nextImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/account/account-panel-arrow_v2.png" alt=">" />
                                                <div class="clear"></div>
                                            </div>

        <?php }?> 
                                            <div class="menuItem menuItemLast menuItemChangePreferences" data-decorator="fnMenuAction" data-url="Customer.changePreferences" data-action="changePreferences">
                                                <img class="iconAction" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/account/icon-change-preferences.png" alt="" /><br />
                                                <div class="menuText">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePreferences');?>

                                                </div>
                                                <img class="nextImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/account/account-panel-arrow_v2.png" alt=">" />
                                                <div class="clear"></div>
                                            </div>

                                        </div> <!-- containerMenuList -->

        <?php if ($_smarty_tpl->tpl_vars['showaccountbalance']->value == true) {?>

                                        <div class="outerBox outerBoxMarginTop outerBoxPadding">

                                            <div class="titleAccountBloc">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccount');?>

                                            </div>

                                            <div class="descriptionAccountBloc">

                                                <div class="labelDescription">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccountBalance');?>
:
                                                </div>

                                                <div class="priceDescription">
                                                    <?php echo $_smarty_tpl->tpl_vars['accountbalance']->value;?>

                                                </div>
                                                <div class="clear"></div>

                                            </div> <!-- descriptionAccountBloc -->

                                            <div class="descriptionAccountBloc">

                                                <div class="labelDescription">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreditLimit');?>
:
                                                </div>

                                                <div class="priceDescription">
                                                    <?php echo $_smarty_tpl->tpl_vars['creditlimit']->value;?>

                                                </div>
                                                <div class="clear"></div>

                                            </div> <!-- descriptionAccountBloc -->

                                        </div> <!-- outerBox outerBoxMarginTop outerBoxPadding -->

        <?php }?> 
        <?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value == true) {?>

                                        <div class="outerBox outerBoxMarginTop">

                                            <div class="outerBoxPadding">

                                                <div class="titleAccountBloc">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardBalance');?>

                                                </div>

                                                <div id="giftcardbalance" class="priceCenter">
                                                    <?php echo $_smarty_tpl->tpl_vars['giftcardbalance']->value;?>

                                                </div>

                                            </div>
                                            <div class="linkRedeem" data-decorator="fnShowPanelAccount" data-visible="true" data-div-id="contentPanelRedeemGiftCard">

                                                <div class="changeBtnText">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeemGiftCard');?>

                                                </div>

                                                <div class="changeBtnImg">
                                                    <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt=">" />
                                                </div>
                                                <div class="clear"></div>

                                            </div>

                                        </div>

        <?php }?> 
        <?php if ($_smarty_tpl->tpl_vars['redactionmode']->value >= 2) {?>

                                        <div class="outerBox outerBoxMarginTop">

                                            <div class="outerBoxPadding">

                                                <div class="titleAccountBloc">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDataDeletion');?>

                                                </div>

                                                <div class="descriptionAccountBloc">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoginToLargeScreenToRequestDataDeletion');?>

                                                </div>

                                            </div>

                                        </div>
        <?php }?> 
                                    </div> <!-- content contentMenu -->

                                </div> <!-- -->

                            </div> <!-- contentHolder -->

                        </div> <!-- contentVisible -->

                    </div> <!-- contentScrollCart -->

                </div> <!-- contentLeftPanel -->

                <div id="contentPanelRedeemGiftCard" class="contentRightPanel">

                    <div id="contentPanelRedeemGiftCardNavigation" class="contentNavigation">

                        <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="contentNavigationMethodList">
                            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
                            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                            <div class="clear"></div>
                        </div>

                    </div>

                    <div id="contentPanelRedeemGiftCardForm" class="contentScrollCart">

                        <div class="contentVisible">

                            <div class="pageLabel">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeemGiftCard');?>

                            </div>

                            <div class="outerBox outerBoxPadding">

                                <div>

                                    <div class="formLine1">
                                        <label for="giftcardid"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardRedeemText');?>
</label>
                                    </div>

                                    <div class="formLine2">
                                        <input type="text" id="giftcardid" class="inputGiftCard" name="giftcardid" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterCode');?>
" data-decorator="fnInputGiftCard" data-trigger="keyup"/>
                                    </div>

                                    <div class="clear"></div>
                                </div>

                            </div> <!-- outerBox outerBoxPadding -->

                            <div class="paddingBtnBottomPage">
                                <div class="btnAction btnContinue" data-decorator="fnRedeemGiftCard">
                                    <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeem');?>
</div>
                                </div>
                            </div>

                        </div> <!-- contentVisible -->

                    </div> <!-- contentScrollCart -->

                </div> <!-- contentPanelRedeemGiftCard -->

                <div id="contentPanelAjax" class="contentRightPanel">

                </div> <!-- contentPanelAjax -->

                <div id="contentPanelShare" class="contentRightPanel">

                    <div id="contentNavigationShare" class="contentNavigation">

                        <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="">
                            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
                            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                            <div class="clear"></div>
                        </div>

                    </div> <!-- contentNavigationShare -->

                    <div id="contentRightScrollShare" class="contentScrollCart">

                        <div class="contentVisible">

                            <div class="animateBloc">

                                <div class="pageLabel" id="shareProjectTitle"></div>

                                <div class="passwordBloc">

                                    <div class="passwordHeader outerBoxPadding">

                                        <div class="passwordLabel">
                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordProtection');?>

                                        </div>

                                        <div class="passwordButton">

                                            <div class="onOffSwitch">
                                                <input type="checkbox" id="sharepassword" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnPasswordDisplay" <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?>checked="checked"<?php }?> />
                                                <label for="sharepassword" class="onOffSwitchLabel">
                                                    <div class="onOffSwitchInner" ontxt="" offtxt=""></div>
                                                    <div class="onOffSwitchButton"></div>
                                                </label>
                                            </div>

                                        </div> <!-- passwordButton -->

                                        <div class="clear"></div>

                                    </div> <!-- passwordHeader outerBoxPadding -->

                                </div> <!-- passwordBloc -->

                            </div> <!-- animateBloc -->

                            <div>

                                <div id="passwordForm" class="passwordForm toggleDiv">

                                    <div>
                                        <div class="formLine1">
                                            <label for="previewPassword" class="labelFloat">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSharePassword');?>
:
                                            </label>
                                            <img id="previewPasswordcompulsory2" class="imgFloat" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="formLine2 password-input-wrap">
                                            <input id="previewPassword" type="password" />
											<button type="button" class="password-visibility password-show" data-decorator="fnTogglePreviewPasword"></button>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>

                                </div> <!-- passwordForm -->

                            </div> <!-- toggleDiv -->

                                <div id="passwordText" class="passwordText">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordProtection');?>

                                </div>
    <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value > 0) {?>

                            <div class="outerBox outerBoxMarginTop outerBoxPadding">
                                 <div class="btnShareType" data-decorator="fnShowShareDetails" data-show="true" data-action="email" data-resize="false">

                                    <div class="changeBtnText">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelShareEmail');?>

                                    </div>

                                    <div class="changeBtnImg">
                                        <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                                    </div>

                                    <div class="clear"></div>

                                </div> <!-- outerBox outerBoxMarginTop outerBoxPadding -->
                            </div>

    <?php }?> 
                             <div class="outerBox outerBoxMarginTop outerBoxPadding">
                                 <div class="btnShareType" data-decorator="fnShowShareDetails" data-show="true" data-action="social" data-resize="false">

                                    <div class="changeBtnText">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelShareSocialNetworks');?>

                                    </div>

                                    <div class="changeBtnImg">
                                        <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                                    </div>

                                    <div class="clear"></div>

                                </div> <!-- linkAction -->
                            </div>

                        </div> <!-- contentVisisble -->

                    </div> <!-- contentScrollCart -->

                </div> <!-- contentPanelShare -->


                <div id="contentPanelShareDetail" class="contentRightPanel">

                    <div id="contentNavigationShareDetail" class="contentNavigation">

                        <div class="btnDoneTop" data-decorator="fnShowShareDetails" data-show="false" data-action="" data-resize="false">
                            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
                            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                            <div class="clear"></div>
                        </div>

                    </div> <!-- contentNavigationShare -->

                    <div id="contentRightScrollShareDetail" class="contentScrollCart">

                        <div class="contentVisible">

                            <div id="shareSocial">

                                <div class="pageLabel">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleShareViaSocialNewtworks');?>

                                </div>

                                <div class="outerBox">
                                    <div id="a2a_menu_container">
                                        <div class="clear"></div>
                                    </div>
                                    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['serverprotocol']->value;?>
static.addtoany.com/menu/page.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
                                </div>
                            </div>

                            <div id="shareEmail">

                                <div class="pageLabel">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleShareViaEmail');?>

                                </div>

                                <div class="outerBox outerBoxPadding">
                                    <div>
                                        <div class="formLine1">
                                            <label for="shareByEmailTitle">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMessageTitle');?>

                                            </label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                        </div>

                                        <div class="formLine2">
                                            <input type="text" id="shareByEmailTitle"/>
                                            <img id="shareByEmailTitlecompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>

                                    </div>

        <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value == 1) {?>

                                    <div class="top_gap">

                                        <div class="formLine1">
                                            <label for="shareByEmailTo">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareWithEmails');?>

                                            </label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                        </div>

                                        <div class="formLine2">
                                            <textarea id="shareByEmailTo" cols="50" rows="2" class="shareByEmailToTextarea"></textarea>
                                            <img id="shareByEmailTocompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>

                                    </div>
                                    <div class="top_gap">
                                        <div class="formLine1">
                                            <label for="shareByEmailText">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareMessageText');?>

                                            </label>
                                            <div class="gap-label-mandatory"></div>
                                        </div>

                                        <div class="formLine2">
                                            <textarea id="shareByEmailText" class="shareByEmailTextTextarea" cols="50" rows="5"></textarea>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

        <?php } else { ?> 
                                    <div class="top_gap">
                                        <div class="formLine1">
                                            <label for="shareByEmailTo">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareWithEmail');?>

                                            </label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                        </div>
                                        <div class="formLine2">
                                            <input type="text" id="shareByEmailTo" class="shareByEmailToInput"/>
                                            <img id="shareByEmailTocompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

                                    <div class="top_gap">

                                        <div class="formLine1">
                                            <label for="shareByEmailText">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareMessageText');?>

                                            </label>
                                            <div class="gap-label-mandatory"></div>
                                        </div>

                                        <div class="formLine2">
                                            <textarea id="shareByEmailText" class="shareByEmailTextInput" cols="50" rows="6"></textarea>
                                            <div class="clear"></div>
                                        </div>

                                    </div>

        <?php }?> 
                                </div> <!-- outerBox outerBoxPadding -->

                                <div class="paddingBtnBottomPage">

                                    <div class="btnAction btnContinue" id="shareByEmailBtn" data-decorator="fnShareByEmail">
                                        <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
                                    </div>

                                </div>

                            </div> <!-- shareEmail -->

                        </div> <!-- contentVisisble -->

                    </div> <!-- contentScrollCart -->

                </div> <!-- contentPanelShare -->

            </div> <!-- contentBlocSite -->

        </div> <!-- outerPage -->

        <form id="submitformmain" name="submitformmain" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
			<input type="hidden" id="tzoffset" name="tzoffset" value="" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        </form>

    </body>

</html><?php }
}
