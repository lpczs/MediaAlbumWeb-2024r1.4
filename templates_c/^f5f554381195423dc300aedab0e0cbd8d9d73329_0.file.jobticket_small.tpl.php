<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:33
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\jobticket_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd9419f99c0_48700419',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f5f554381195423dc300aedab0e0cbd8d9d73329' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\jobticket_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_small.tpl' => 1,
    'file:order/jobticket.tpl' => 1,
    'file:order/jobticketajax_small.tpl' => 2,
  ),
),false)) {
function content_69abd9419f99c0_48700419 (Smarty_Internal_Template $_smarty_tpl) {
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
 - <?php echo $_smarty_tpl->tpl_vars['title']->value;?>
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
            

                // dialogbox status
                var gDialogIsOpen = false;

                // slide panel
                var gActivePanel = '';
                var gPreviousPanel = '';

                // qty navigation
                var gOrderlineidActive = '';
                var gComponentActive = '';
                var gSubComponentActive = '';

                // app width
                var gScreenWidth = 0;
                var gMaxWidth = 600;
                var gHighLightBorderSizeDifference = 0;

                var gContentScrollCart = 0;

                var gOuterBox = 0;
                var gOuterBoxPadding = 0;

                var gInnerBox = 0;
                var gInnerBoxPaddding = 0;
                var gInnerBoxDescription = 0;

                var gOuterBoxContentBloc  = 0;
                var gInnerBoxContentBloc = 0;

                var gComponentPreview = 0;

                var gTickImageWidth = 0;

                // scroll position
                var gScrollPositionComponent = 0;
                var gScrollPositionShipping = 0;
                var gScrollRefreshPosition = 0;

                // navigation
                var gPreviousHash = '';
                var gCurrentSource = 'jobticket';
                var gForceBack = false;

                window.addEventListener('DOMContentLoaded', function(event) {
                      initialize();

                      document.body.onresize = function(e) {
                          resizeApp();
                      };

                      document.body.addEventListener('click', decoratorListener);
                      document.body.addEventListener('keyup', decoratorListener);
                      document.body.addEventListener('keypress', decoratorListener);
                      document.body.addEventListener('change', decoratorListener);
                });

/*** GENERIC ***/

                /**
                 * initialize
                 *
                 * initialize the application
                 */
                function initialize()
                {
                    // listen hash url changes
                    window.onhashchange = locationHashChanged;

                    //prompt the loading dialog
                    showLoadingDialog();

                    gActiveResizeFormID = new Date().getTime();

                    // inititalize the stage
                    initializeApplication(true);

                    //function to handle the pressing of the go button in android and return in ios also on keyboard
                    addEventListener("keydown", function (e)
                    {
                        if (e.keyCode == 13)
                        {
                            // execute specific action compare to the form a user is on
                            switch(window.location.hash)
                            {
                                case "#changeBillingAddress":
                                    verifyAddress();
                                    break;
                                case "#changeShippingAddress":
                                    verifyAddress();
                                    break;
                            }
                        }
                    }, false);

                    // Load the panel requested compare to the hash in url
                    if (window.location.hash != '')
                    {
                        var currentHash = window.location.hash.split('|');
                        switch(currentHash[0])
                        {
//qty
                            case '#componentView':

                                initializeStage();

                                gActivePanel = 'contentPanelComponent';
                                showPanelQty(true, 'contentPanelComponent', currentHash[1], currentHash[2]);
                                break;
                            case '#subComponentView':

                                initializeStage();

                                // show the container of the componet for a line order
                                document.getElementById('componentContainer' + currentHash[1]).style.display = 'block';
                                // show the component selected
                                document.getElementById('componentDetail_' + currentHash[2]).style.display = 'block';

                                // slide the qty panel
                                document.getElementById('contentPanelQty').style.marginLeft = '-' + gScreenWidth + 'px';

                                gComponentActive = currentHash[2];

                                gActivePanel = 'contentPanelSubComponent';
                                showPanelQty(true, 'contentPanelSubComponent', currentHash[1], currentHash[3]);

                                initializeComponentView('componentDetail_' + currentHash[2]);

                                document.getElementById('contentPanelComponent').style.display = 'block';
                                break;

                            case '#componentChoice':
                                gOrderlineidActive = currentHash[1];
                                gComponentActive = currentHash[2];
                                gActivePanel = 'contentPanelComponent';

                                document.getElementById('choiceBackButton').onclick = function(){
                                    setHashUrl('componentView|' + currentHash[1] + '|' + currentHash[2]);
                                };

                                initializeStage();

                                // send the ajax
                                processAjaxSmallScreen("componentChangeListRefresh",".?fsaction=AjaxAPI.callback&cmd=CHANGECOMPONENTSMALL&item=" + currentHash[1] + "&section=" + currentHash[2], 'GET', '');

                                break;
                            case '#subComponentChoice':
                                gOrderlineidActive = currentHash[1];
                                gComponentActive = currentHash[2];
                                gSubComponentActive = currentHash[3];
                                gActivePanel = 'contentPanelSubComponent';

                                initializeStage();

                                // init the back action
                                document.getElementById('choiceBackButton').onclick = function(){
                                    setHashUrl('subComponentView|' + currentHash[1] + '|' + currentHash[2] + '|' + currentHash[3]);
                                };

                                // send the ajax
                                processAjaxSmallScreen("componentChangeListRefresh",".?fsaction=AjaxAPI.callback&cmd=CHANGECOMPONENTSMALL&item=" + currentHash[1] + "&section=" + currentHash[3], 'GET', '');
                            break;
// shipping
                            case '#shipping':
                                loadShippingPanel('showShipping');
                            break;

                            case '#changeMethod':
                                loadShippingPanel('changeMethod');
                            break;

                            case '#store':
                                loadShippingPanel('store');
                            break;

                            case '#changeBillingAddress':
                                loadShippingPanel('changeBillingAddress');
                            break;

                            case '#changeShippingAddress':
                                loadShippingPanel('changeShippingAddress');
                            break;

// payment
                            case '#payment':
                                loadShippingPanel('showPaymentPanel');
                            break;

                            default:
                                resetHash();
                        }

                        gPreviousHash = currentHash[0];
                    }
                    else
                    {
                        resetHash();
                    }
                }

                function resetHash()
                {
                    gPreviousHash = '#' + gOrderStage;

                    switch(gPreviousHash)
                    {
                        case '#qty':
                            initializeStage();
                            initializeQtyPanel();
                        break;
                        case '#shipping':
                            loadShippingPanel('showShipping');
                        break;
                        case '#payment':
                            loadShippingPanel('showPaymentPanel');
                        break;
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
                    var currentHash = window.location.hash.split('|');
                    var currentAction = currentHash[0];
                    if ((currentAction == '') || (currentAction == '#'))
                    {
                        currentAction = '#' + gOrderStage;
                    }

                    switch(currentAction)
                    {
                        case '#cancellation':
                            showCancellation();
                        break;
                        case '#confirmation':
                            showConfirmation();
                        break;
//qty
                        case '#qty':
                            switch (gPreviousHash)
                            {
                                case '#shipping':
                                    showPreviousQty();
                                break;
                                default:
                                    // slide back to the main panel
                                    showPanelQty(false, '', '', '');
                            }
                        break;
                        case '#componentView':
                            if (gPreviousHash == '#qty')
                            {
                                showPanelQty(true, 'contentPanelComponent', currentHash[1], currentHash[2]);
                            }
                            else
                            {
                                showPanelQty(false, '', '', '');
                            }
                        break;
                        case '#subComponentView':
                            if (gPreviousHash == '#componentView')
                            {
                                showPanelQty(true, 'contentPanelSubComponent', currentHash[1], currentHash[3]);
                            }
                            else
                            {
                                showPanelQty(false, '', '', '');
                            }
                        break;
                        case '#componentChoice':
                        case '#subComponentChoice':
                            if ((gPreviousHash == '#componentView') || (gPreviousHash == '#subComponentView'))
                            {
                                showPanelQty(true, 'contentPanelComponentChoice', gOrderlineidActive, '');
                            }
                        break;

// shipping
                        case '#shipping':

                            if (typeof gCollectFromStoreCode !== 'undefined')
                            {
                                if ((gCollectFromStore == '1') && (gCollectFromStoreCode == ''))
                                {
                                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoStore');?>
", function(e) {
                                        closeDialog(e);
                                    });
                                    setHashUrl('changeMethod');
                                }
                            }

                            if (gPreviousHash == '#qty')
                            {
                                loadShippingPanel('showShipping');
                            }
                            else
                            {
                                if (gPreviousHash == '#payment')
                                {
                                    showPreviousShipping();
                                }
                                else
                                {
                                    showPanelShipping(false, '');
                                }
                            }
                        break;
                        case '#changeMethod':
                            if (gPreviousHash == '#shipping')
                            {
                                showPanelShipping(true, 'contentPanelMethodList');
                            }
                            else
                            {
                                gPreviousHash = currentHash[0];
                                showPanelShipping(false, '');
                            }
                        break;
                        case '#store':
                            // show loading dialog
                            showLoadingDialog();
                            selectStore(currentHash[1]);
                        break;
                        case '#changeBillingAddress':
                            // open the loading box
                            showLoadingDialog();
                            changeBillingAddress();
                        break;
                        case '#changeShippingAddress':
                            // open the loading box
                            showLoadingDialog();
                            changeShippingAddress();
                        break;
                        case '#changeShippingAddressContactDetails':
                            // open the loading box
                            showLoadingDialog();
                            changeShippingAddress('CFS');
                        break;
// payment
                        case '#payment':
                            // test if the user come from shipping if not something went wrong
                            if (gPreviousHash == '#shipping')
                            {
                                loadPaymentPanel();
                            }
                            else
                            {
								setHashUrl('qty');
                            }
                        break;
                    }

                    gPreviousHash = currentAction;
                }

                // wrapper for setHashUrl
                function fnSetHashUrl(pElement)
                {
                    if (!pElement) {
                        return false;
                    }

                    return setHashUrl(pElement.getAttribute('data-hash-url'));
                }

                /**
                * setHashUrl
                *
                * set the hash of the url
                */
                function setHashUrl(pHash)
                {
                    gActiveResizeFormID = new Date().getTime();
                    window.location.hash = pHash;
                }

                function resizeApplication()
                {
                    // reset the scroll position to force the page to be at the top when displayed
                    gScreenWidth = 0;
                    gScrollPositionComponent = 0;

                    // inititalize the stage
                    initializeApplication(false);
                    initializeStage();

                    // execute a different action compare the the current panel
                    var currentHash = window.location.hash.split('|');
                    var currentAction = currentHash[0];
                    if ((currentAction == '') || (currentAction == '#'))
                    {
                        currentAction = '#' + gOrderStage;
                    }

                    switch(currentAction)
                    {
                        case '#cancellation':
                            showCancellation();
                        break;
                        case '#confirmation':
                            showConfirmation();
                        break;

                        case '#qty':
                            initializeQtyPanel();
                        break;

                        case '#componentView':
                            initializeQtyPanel();
                            // show the panel
                            showPanelQty(true, 'contentPanelComponent', gOrderlineidActive, gComponentActive);
                        break;

                        case '#subComponentView':
                            initializeQtyPanel();

                            // slide main panel and component panel
                            document.getElementById('contentPanelQty').style.marginLeft = '-' + gScreenWidth + 'px';
                            document.getElementById('contentPanelComponent').style.marginLeft = '-' + gScreenWidth + 'px';

                            // show subcomponent panel
                            showPanelQty(true, 'contentPanelSubComponent', gOrderlineidActive, gSubComponentActive);

                            // force the callback mode
                            gActivePanel = 'contentPanelSubComponentBack';
                        break;

                        case '#componentChoice':
                        case '#subComponentChoice':

                            initializeQtyPanel();

                            // slide main panel and component panel
                            document.getElementById('contentPanelQty').style.marginLeft = '-' + gScreenWidth + 'px';
                            document.getElementById('contentPanelComponent').style.marginLeft = '-' + gScreenWidth + 'px';

                            if ((gPreviousPanel == 'contentPanelSubComponent') || (gPreviousPanel == 'contentPanelSubComponentBack'))
                            {
                                document.getElementById('contentPanelSubComponent').style.marginLeft = '-' + gScreenWidth + 'px';
                            }

                            // change the active panel because at this point the active panel is the previous panel
                            gActivePanel = gPreviousPanel;

                            // show the component choice panel
                            showPanelQty(true, 'contentPanelComponentChoice', gOrderlineidActive, '');
                        break;

                        case '#shipping':
                            resizeQtyPanel();

							// set the scroll area
							setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');
                        break;

                        case '#changeMethod':
                            resizeQtyPanel();

                            // show the panel method list
                            showPanelShipping(true, 'contentPanelMethodList');
                        break

                        case '#store':
                            resizeQtyPanel();

                            // resize form and results elements
                            resizeResultElement();

                            //show the select store panel
                            showPanelShipping(true, 'contentPanelSelectStore');

                            document.getElementById('contentPanelMethodList').style.display = "none";
                        break;

                        case '#changeBillingAddress':
                        case '#changeShippingAddress':
                            resizeQtyPanel();

                            // show address form panel
                            showPanelShipping(true, 'contentPanelUpdateAddress');
                        break;

                        case '#payment':
                            switch(gActivePanel)
                            {
                                case 'showConfirmation':
                                    setHashUrl('confirmation');
                                break;

                                case 'showPaymentgateway':
                                    // show the payment gateway panel
                                    var contentPanelPaymentgateway = document.getElementById('contentPanelPaymentgateway');
                                    contentPanelPaymentgateway.style.width = gScreenWidth + 'px';
                                    contentPanelPaymentgateway.style.display= 'block';

                                    // slide the payment panel
                                    var contentPanelPayment = document.getElementById('contentPanelPayment');
                                    contentPanelPayment.style.marginLeft = '-' + gScreenWidth + 'px';
                                break;

                                case 'showCancellation':
                                    setHashUrl('cancellation');
                                break;
								default:
									initializeStage();
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
                    var currentHash = window.location.hash.split('|');
                    var currentAction = currentHash[0];
                    if ((currentAction == '') || (currentAction == '#'))
                    {
                        currentAction = '#' + gOrderStage;
                    }

                    switch(currentAction)
                    {
                        case '#qty':
                            setContentVisibleHeight('contentLeftScrollQty');
                        break;

                        case '#componentView':
                            if (gOrderlineidActive == -1)
                            {
                            	setContentVisibleHeight('contentRightScrollOrderFooterComponentDetail');
                            }
                            else
                            {
                            	setContentVisibleHeight('contentRightScrollComponentDetail_' + gOrderlineidActive);
                            }
                        break;

                        case '#subComponentView':
							if (gOrderlineidActive == -1)
                            {
                            	setContentVisibleHeight('contentRightScrollOrderFooterSubComponentDetail');
                            }
                            else
                            {
                            	setContentVisibleHeight('contentRightScrollSubComponentDetail_' + gOrderlineidActive);
                            }
                        break;

                        case '#store':
                            if (gKeyboardOpen == false)
                            {
                                setContentVisibleHeight('contentStoreList');
                            }
                            break;

                        case '#changeBillingAddress':
                        case '#changeShippingAddress':
                            setContentVisibleHeight('contentRightScrollAddress');
                        break;

                        case '#payment':
                            if (gKeyboardOpen == false)
                            {
                                setContentVisibleHeight('contentLeftScrollPayment');
                            }
                        break;
                    }
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

                function forceRedirectionToLogin(pMessage)
                {
                    if (pMessage != '')
                    {
                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", pMessage, function(e) {
                            redirectToLogin();
                        });
                    }
                    else
                    {
                        redirectToLogin();
                    }
                }


                function redirectToLogin()
                {
					var currentUrl = window.location.href.replace(window.location.hash, '');
					// force the page to be reloaded
					if (currentUrl == "<?php echo $_smarty_tpl->tpl_vars['redirecturl']->value;?>
")
					{
						window.location.hash = '';
						// prevent to read from cache
						window.location.reload(true);
					}
					else
					{
						window.location.href = "<?php echo $_smarty_tpl->tpl_vars['redirecturl']->value;?>
";
					}
                }

                /**
                 * processAjaxSmallScreen
                 *
                 * send an ajax query to the server and get the respond back
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
                                case 'termsandconditionswindow':
                                    // show a dailog bowx with the HTML from teh server
                                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleTermsAndConditions');?>
", unescape(xmlhttp.responseText), function(e) {
                                        closeDialog(e);
                                    });
                                    document.getElementById('dialogContent').className = document.getElementById('dialogContent').className + ' contentFormTermsAndCondition';
									break;
                                case 'storeInfo':
                                    // show a dailog bowx with the HTML from teh server
                                    openDialog(unescape(xmlhttp.responseText));
                                    break;

                                case 'componentChangeList':
                                    document.getElementById('contentVisibleChoice').innerHTML = xmlhttp.responseText;

                                    // detect which panel need to be slided to
                                    if ((gActivePanel == 'contentPanelComponent') || (gActivePanel == 'contentPanelComponentBack'))
                                    {
                                        setHashUrl('componentChoice|' + gOrderlineidActive + '|' + gComponentActive);
                                    }
                                    else
                                    {
                                        setHashUrl('subComponentChoice|' + gOrderlineidActive + '|' + gComponentActive + '|' + gSubComponentActive);
                                    }
                                    break;
                                case 'componentChangeListRefresh':
                                    document.getElementById('contentVisibleChoice').innerHTML = xmlhttp.responseText;

                                    gPreviousPanel = 'contentPanelComponent';

                                    // detect which panel need to be slided
                                    if ((gActivePanel != 'contentPanelComponent') && (gActivePanel != 'contentPanelComponentBack'))
                                    {

                                        gPreviousPanel = 'contentPanelSubComponent';
                                     }

                                    showPanelQty(true, 'contentPanelComponentChoice', gOrderlineidActive, '');

                                    // slide the qty panel
                                    document.getElementById('contentPanelQty').style.marginLeft = '-' + gScreenWidth + 'px';

                                    var contentPanelComponent = document.getElementById('contentPanelComponent');
                                    contentPanelComponent.style.marginLeft = '-' + gScreenWidth + 'px';
                                    contentPanelComponent.style.display = "block";

                                    // show the container of the componet for a line order
                                    document.getElementById('componentContainer' + gOrderlineidActive).style.display = 'block';

                                    // detect which panel need to be slided
                                    if (gPreviousPanel == 'contentPanelSubComponent')
                                    {
                                        document.getElementById('contentPanelSubComponent').style.display = "block";
                                        // show the container of the componet for a line order
                                        document.getElementById('subcomponentContainer' + gOrderlineidActive).style.display = 'block';
                                     }

                                    break;
                                // update selcted store
                                case 'selectStore':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    var storeAddress = jsonObj['storeaddress'];
                                    var storeId = jsonObj['storeid'];
                                    var shippingCode = getShippingRateCode();

                                    gStoreAddresses[shippingCode] = storeAddress;
                                    gStoreCodes[shippingCode] = storeId;

                                    gCollectFromStoreCode = storeId;

                                    // update the address
                                    if (storeAddress != '')
                                    {
                                        var contentPanelMethodList = document.getElementById('contentPanelMethodList');
                                        contentPanelMethodList.getElementsByClassName('optionSelected')[0].getElementsByTagName('label')[0].getElementsByTagName('span')[0].innerHTML = storeAddress + '<br />';
                                    }

                                    // refresh the content of the shipping page if we change the
                                    document.getElementById('contentPanelShipping').innerHTML = jsonObj.template;

                                    window.history.back();

                                    break;

                                // slide to the select store search form
                                case 'storeLocatorForm':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    //load the content of the template
                                    document.getElementById('contentPanelSelectStore').innerHTML = jsonObj.templateform;

                                    // refresh the shipping panel
                                    document.getElementById('contentPanelShipping').innerHTML = jsonObj.shipping.template;

                                    // copy javascript content
                                    toggleJs('includeSelectStoreJS', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                    // show the panel
                                    showPanelShipping(true, 'contentPanelSelectStore');
                                    break;

                                case 'changegiftcard':
                                    var jsonObj = parseJson(xmlhttp.responseText);
                                    if (jsonObj.success)
                                    {
                                        gCanUseAccount = jsonObj.canuseaccount;

                                        var giftcard = document.getElementById("giftcard");
                                        var giftcardamount = document.getElementById("giftcardamount");
                                        var giftcardbutton = document.getElementById("giftbutton");
                                        var ordertotaltopayvalue = document.getElementById("ordertotaltopayvalue");
                                        var paymenttableobj = document.getElementById("paymenttableobj");
                                        var giftcardbalance = document.getElementById("giftcardbalance");
                                        var includetaxtextwithgiftcard = document.getElementById("includetaxtextwithgiftcard");
                                        var includetaxtextwithoutgiftcard = document.getElementById("includetaxtextwithoutgiftcard");

                                        if (giftcardbalance)
                                        {
                                            giftcardbalance.innerHTML = jsonObj.giftcardtotalremaining;
                                        }

                                        if (paymenttableobj)
                                        {
                                            if (jsonObj.hidepayment)
                                            {
                                                paymenttableobj.style.display = 'none';
                                                gPaymentMethodCode = 'NONE';
                                            }
                                            else
                                            {
                                                paymenttableobj.style.display = '';
                                                gPaymentMethodCode = "{$paymentmethodcode}";
                                            }
                                        }

                                        // update the amout to pay
                                        if (ordertotaltopayvalue)
                                        {
                                            ordertotaltopayvalue.innerHTML = jsonObj.ordertotaltopay;
                                        }

                                        // swicth the gift car visibility
                                        if (jsonObj.giftcardstate == 'add')
                                        {
                                            giftcard.className = 'disabled';
                                            giftcardamount.className = 'totalNumber disabled';
                                            giftcardbutton.className = "classGiftadd";

                                            if (includetaxtextwithgiftcard)
                                            {
                                                includetaxtextwithgiftcard.style.display = 'none';
                                            }

                                            if (includetaxtextwithoutgiftcard)
                                            {
                                                includetaxtextwithoutgiftcard.style.display = '';
                                            }
                                        }
                                        else
                                        {
                                            giftcard.className = '';
                                            giftcardamount.className = 'totalNumber';
                                            giftcardbutton.className = "classGiftdelete";

                                            if (includetaxtextwithgiftcard)
                                            {
                                                includetaxtextwithgiftcard.style.display = '';
                                            }

                                            if (includetaxtextwithoutgiftcard)
                                            {
                                                includetaxtextwithoutgiftcard.style.display = 'none';
                                            }

                                        }

                                        if (giftcardamount)
                                        {
                                            giftcardamount.innerHTML = jsonObj.giftcardamount;
                                        }
                                    }
                                    closeLoadingDialog();
                                    break;

                                case 'updateComponent':
                                    var response = parseJson(xmlhttp.responseText);

                                    // change the orderfooter
                                    var orderFooter = document.getElementById('orderFooter');
                                    if(orderFooter)
                                    {
                                        document.getElementById('orderFooter').innerHTML = unescape(decodeURIComponent(response.orderFooterHTML));
                                    }

                                    // change the item line
                                    if (gOrderlineidActive != -1)
                                    {
                                        document.getElementById("ordertableobj_" + gOrderlineidActive).innerHTML = unescape(decodeURIComponent(response.orderLineHTML));
                                    }

                                    // update order datas
                                    gOrderData[response.data.orderlineid] = response.data;
                                    gOrderCanContinue.ordercancontinue = response.ordercancontinue;

                                    // change the component panel
                                    var contentPanelComponent = document.getElementById('componentContainer' + gOrderlineidActive);
                                    contentPanelComponent.innerHTML = unescape(decodeURIComponent(response.componentDetailHTML));

                                    // change the subcomponent panel
                                    var contentPanelSubComponent = document.getElementById('subcomponentContainer' + gOrderlineidActive);
                                    contentPanelSubComponent.innerHTML = unescape(decodeURIComponent(response.subcomponentDetailHTML));

                                    // get the new id from the server for the subcomponent
                                    gSubComponentActive = response.data.sectionorderlineid;

                                    //update order total
                                    document.getElementById("orderTotal").innerHTML = response.orderTotal;

                                    if ((gPreviousPanel == 'contentPanelComponent') || (gPreviousPanel == 'contentPanelComponentBack'))
                                    {
                                        // show the component panel
                                        document.getElementById('componentDetail_' + gComponentActive).style.display = 'block';

                                        if (gOrderlineidActive == -1)
                                        {
                                            setScrollAreaHeight('contentRightScrollOrderFooterComponentDetail', 'contentNavigationOrderFooterComponentDetail');
                                        }
                                        else
                                        {
                                            setScrollAreaHeight('contentRightScrollComponentDetail_' + gOrderlineidActive, 'contentNavigationComponentDetail');
                                        }

                                        setHashUrl('componentView|' + gOrderlineidActive + '|' + gComponentActive);
                                    }
                                    else
                                    {
                                        document.getElementById('subcomponentDetail_' + gSubComponentActive).style.display = 'block';
                                        if (gOrderlineidActive == -1)
                                        {
                                            setScrollAreaHeight('contentRightScrollOrderFooterSubComponentDetail', 'contentNavigationOrderFooterSubComponentDetail');
                                        }
                                        else
                                        {
                                            setScrollAreaHeight('contentRightScrollSubComponentDetail_' + gOrderlineidActive, 'contentNavigationSubComponentDetail');
                                        }

                                        setHashUrl('subComponentView|' + gOrderlineidActive + '|' + gComponentActive + '|' + gSubComponentActive);
                                    }
                                    break;

                                case 'updateComponentQty':
                                case 'updateQty':
                                case 'updateCheckBox':
                                    var response = parseJson(xmlhttp.responseText);

                                    // change the orderfooter line
                                    var orderFooter = document.getElementById('orderFooter');
                                    if(orderFooter)
                                    {
                                        document.getElementById('orderFooter').innerHTML = unescape(decodeURIComponent(response.orderFooterHTML));
                                    }

                                    // change the item line
                                    if (gOrderlineidActive != -1)
                                    {
                                        document.getElementById("ordertableobj_" + gOrderlineidActive).innerHTML = unescape(decodeURIComponent(response.orderLineHTML));
                                    }

                                    // update order datas
                                    gOrderData[response.data.orderlineid] = response.data;
                                    gOrderCanContinue.ordercancontinue = response.ordercancontinue;

                                    //update order total
                                    document.getElementById("orderTotal").innerHTML = response.orderTotal;

                                    // display voucher message
                                    if( (response.vouchermessage != null) && (response.vouchermessage.length > 0))
                                    {
                                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmation');?>
", response.vouchermessage, function(e) {
                                            closeDialog(e);
                                        });
                                    }

                                    // change the component panel
                                    var contentPanelComponent = document.getElementById('componentContainer' + gOrderlineidActive);
                                    contentPanelComponent.innerHTML = unescape(decodeURIComponent(response.componentDetailHTML));

                                    // change the subcomponent panel
                                    var contentPanelSubComponent = document.getElementById('subcomponentContainer' + gOrderlineidActive);
                                    contentPanelSubComponent.innerHTML = unescape(decodeURIComponent(response.subcomponentDetailHTML));

                                    // set the application html elements size
                                    initializeStage();
                                    initializeQtyPanel();

                                    // check if is not an item qty changed
                                    if (obj != 'updateQty')
                                    {
                                        // if it's a component qty changed the selected id need to be update
                                        if (obj == 'updateComponentQty')
                                        {
                                            if ((gActivePanel == 'contentPanelSubComponent') || (gActivePanel == 'contentPanelSubComponentBack'))
                                            {
                                                gSubComponentActive = response.data.sectionorderlineid;
                                            }
                                            else
                                            {
                                                gComponentActive = response.data.sectionorderlineid;
                                            }
                                        }

                                        // check if the component still exists
                                        var componentDetail = document.getElementById('componentDetail_' + gComponentActive);
                                        if (componentDetail)
                                        {
                                            componentDetail.style.display = 'block';

                                            // check if we are manipulating the orderfooter
                                            if (gOrderlineidActive == -1)
                                            {
                                                setScrollAreaHeight('contentRightScrollOrderFooterComponentDetail', 'contentNavigationOrderFooterComponentDetail');
                                            }
                                            else
                                            {
                                                setScrollAreaHeight('contentRightScrollComponentDetail_' + gOrderlineidActive, 'contentNavigationComponentDetail');
                                            }

                                            // set the component html elements size
                                            initializeComponentView('componentDetail_' + gComponentActive);
                                        }

                                        // check if war are manipulating a subcomponent
                                        if ((gActivePanel == 'contentPanelSubComponent') || (gActivePanel == 'contentPanelSubComponentBack'))
                                        {
                                            // check if the subcomponent still exists
                                            var componentDetail = document.getElementById('subcomponentDetail_' + gSubComponentActive);
                                            if (componentDetail)
                                            {
                                                componentDetail.style.display = 'block';

                                                // set the subcomponent html elements size
                                                initializeComponentView('subcomponentDetail_' + gSubComponentActive);
                                            }

                                            // check if we are manipulating orderfooter
                                            if (gOrderlineidActive == -1)
                                            {
                                                setScrollAreaHeight('contentRightScrollOrderFooterSubComponentDetail', 'contentNavigationOrderFooterSubComponentDetail');
                                            }
                                            else
                                            {
                                                setScrollAreaHeight('contentRightScrollSubComponentDetail_' + gOrderlineidActive, 'contentNavigationSubComponentDetail');
                                            }
                                        }
                                    }

                                    if (gActivePanel == '')
                                    {
                                        document.getElementById('contentLeftScrollQty').scrollTop = gScrollRefreshPosition;
                                    }
                                    else if(gOrderlineidActive == -1)
                                    {
                                        document.getElementById('contentRightScrollOrderFooterSubComponentDetail').scrollTop = gScrollRefreshPosition;
                                    }
                                    else
                                    {
                                        document.getElementById('contentRightScrollComponentDetail_' + gOrderlineidActive).scrollTop = gScrollRefreshPosition;
                                    }

                                    if ((obj == 'updateComponentQty') && gForceBack)
                                    {
                                        gForceBack = false;

                                        if ((gActivePanel == 'contentPanelSubComponent') || (gActivePanel == 'contentPanelSubComponentBack'))
                                        {
                                            document.getElementById('contentRightScrollComponentDetail_' + gOrderlineidActive).scrollTop = gScrollPositionComponent;

                                            setHashUrl('componentView|' + gOrderlineidActive + '|' + gComponentActive);
                                        }
                                        else
                                        {
                                            setHashUrl('qty');
                                        }
                                    }
                                    else
                                    {
                                        // close the loading box
                                        closeLoadingDialog();
                                    }
                                    break;

                                // slide to the shipping panel
                                case 'showShipping':
                                case 'changeMethod':
                                case 'store':
                                case 'changeBillingAddress':
                                case 'changeShippingAddress':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
                                        // load javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                        // load the HTML
                                        var contentAjaxShipping = document.getElementById('contentAjaxShipping');
                                        contentAjaxShipping.innerHTML = jsonObj.template;

                                        // set the width of the qty panel
                                        var contentAjaxQty = document.getElementById('contentAjaxQty');
                                        contentAjaxQty.style.width = gScreenWidth + 'px';

                                        // set the content width
                                        initializeStage();

                                        resizeQtyPanel();

                                        // slide the qty panel
                                        contentAjaxQty.style.marginLeft = '-' + gScreenWidth + 'px';

                                        switch (obj)
                                        {
                                            case 'showShipping':
                                                document.getElementById('contentPanelShipping').style.display = 'block';
                                                setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');
                                                gActivePanel = 'showShipping';
                                            break;
                                            case 'changeMethod':
                                                showPanelShipping(true, 'contentPanelMethodList');
                                            break;
                                            case 'store':
                                                document.getElementById('contentPanelShipping').style.marginLeft = '-' + gScreenWidth + 'px';
                                                var currentHash = window.location.hash.split('|');
                                                selectStore(currentHash[1]);
                                            break;
                                            case 'changeBillingAddress':
                                                document.getElementById('contentPanelShipping').style.marginLeft = '-' + gScreenWidth + 'px';
                                                changeBillingAddress();
                                            break;
                                            case 'changeShippingAddress':
                                                document.getElementById('contentPanelShipping').style.marginLeft = '-' + gScreenWidth + 'px';
                                                changeShippingAddress();
                                            break;
                                        }
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
                                    }
                                break;

                                case 'showPaymentPanel':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
                                        // load javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                        // load the HTML
                                        document.getElementById('contentAjaxShipping').innerHTML = jsonObj.template;

                                        // set the width of the qty panel
                                        var contentAjaxQty = document.getElementById('contentAjaxQty');
                                        contentAjaxQty.style.width = gScreenWidth + 'px';

                                        // slide the qty panel
                                        contentAjaxQty.style.marginLeft = '-' + gScreenWidth + 'px';

                                        document.getElementById('contentPanelShipping').style.marginLeft = '-' + gScreenWidth + 'px';
                                        loadPaymentPanel();
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
                                    }
                                break;


                                // slide to the qty panel
                                case 'backQty':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
                                        // load javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                        // load the HTML
                                        var contentAjaxQty = document.getElementById('contentAjaxQty');
                                        contentAjaxQty.innerHTML = jsonObj.template;

                                        // change the size of the previous panel for the slide effect
                                        document.getElementById('contentAjaxShipping').style.width = gScreenWidth + 'px';

                                        document.getElementById('contentPanelQty').style.display = "block";

                                        // set the content width
                                        initializeStage();
                                        initializeQtyPanel();

                                        // slide effect
                                        document.getElementById('contentAjaxQty').style.marginLeft = 0;
                                        document.getElementById('contentAjaxShipping').innerHTML = '';
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
                                    }
                                break;

                                // slide to the payment panel
                                case 'showPayment':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
                                        // change the size of the previous panel for the slide effect
                                        var contentAjaxPayment = document.getElementById('contentAjaxPayment');
                                        contentAjaxPayment.innerHTML = jsonObj.template;

                                        // change javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');
                                        
                                        // We need to include all the relevant script urls and scripts for each payment gateway.
                                        // This is to handle the situation where both PayPalPlus and another gateway that requires javascript are enabled.
                                        for (var paymentmethodCode in jsonObj.paymentgatewayjavascriptarray)
                                        {                                            
                                            var scriptURL = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('scripturl') ? jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].scripturl : '';
                                            var scriptForm = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('form') ? jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].form : '';
                                            var script = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('script') ? scriptForm + ' ' + jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].script : '';
                                            var integrity = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('hash') ? jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hash : '';
                                            var requestPaymentParamsRemotely = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].requestpaymentparamsremotely;

                                            if (scriptURL !== '')
                                            {
                                                toggleJs('externalscript_' + paymentmethodCode, '', true, scriptURL, '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
', script, integrity);
                                            }

                                            if ((script !== '') && (scriptURL === ''))
                                            {
                                                toggleJs('paymentjavascript_' + paymentmethodCode, script, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');
                                            }

											// Add the flag for lightbox payment gateways, this needs to be execute everytime the page is loaded.
											if (requestPaymentParamsRemotely)
											{
												toggleJs('requestparamsremotelyjavascript_' + paymentmethodCode, 'gRequestPaymentParamsRemotely = true;', true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');
											}
                                        }

                                        // change the size of the previous panel for the slide effect
                                        var contentAjaxShipping = document.getElementById('contentAjaxShipping');
                                        contentAjaxShipping.style.width = gScreenWidth + 'px';

                                        // initialize the stage html elments
										initializeStage();

										// slide effect
										contentAjaxShipping.style.marginLeft = '-' + gScreenWidth + 'px';
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
                                    }
                                break;

                                // slide to the shipping panel
                                case 'backShipping':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
                                        // change javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                        var contentAjaxShipping = document.getElementById('contentAjaxShipping');
                                        contentAjaxShipping.innerHTML = jsonObj.template;

                                        // initialize the stage html elments
                                        initializeStage();
                                        resizeQtyPanel();

                                        // slide effect and empty the previous panel
                                        document.getElementById('contentPanelShipping').style.display = "block";

                                        setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');

                                        document.getElementById('contentAjaxShipping').style.marginLeft = 0;
                                        document.getElementById('contentAjaxPayment').innerHTML = '';
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
                                    }
                                break;

                                // slide to the update address panel
                                case 'changeBillingAddressDisplay':
                                case 'changeShippingAddressDisplay':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    //load the content of the template
                                    document.getElementById('contentPanelUpdateAddress').innerHTML = jsonObj.template;

                                    // copy javascript content
                                    toggleJs('includeUpdateAddressJS', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                    // load the initialize function for the address form
                                    initializeAddress(true, 'contentNavigationAddress');

                                    // show the panel
                                    showPanelShipping(true, 'contentPanelUpdateAddress');
                                break;

                                // Refresh the shipping panel or prompt the update address
                                case 'changeshippingmethod':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    // check if the address need to be displayed
                                    if (jsonObj.forcechangeaddressdisplay == true)
                                    {
                                        setHashUrl('changeShippingAddress');
                                    }
                                    else
                                    {
                                        document.getElementById('contentPanelShipping').innerHTML = jsonObj.template;

                                        // initialize the stage html elments
                                        initializeStage();
                                    }
                                break;

								case 'cfsfixedchangeshippingmethod':
                                    var jsonObj = parseJson(xmlhttp.responseText);
									
                                   	 // check if the address need to be displayed
                                    if (jsonObj.forcechangeaddressdisplay == true)
                                    {
                                        setHashUrl('changeShippingAddress');
                                    }
                                    else
                                    {
                                        document.getElementById('contentPanelShipping').innerHTML = jsonObj.template;

                                        // initialize the stage html elments
                                        initializeStage();
                                    }
									   
									gCollectFromStoreCode = gStoreCodes[gShippingRateCode];
                                break;

                                case 'copyShippingAddress':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    document.getElementById('contentPanelShipping').innerHTML = jsonObj.template;

                                    // change javascript
                                    toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                    // initialize the stage html elments
                                    initializeStage();

									setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');
                                break;

                                // slide to the confirmation page
                                case 'showConfirmation':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    // change javascript
                                    toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                    // show the confirmation panel
                                    var contentPanelConfirmation = document.getElementById('contentPanelConfirmation');
                                    contentPanelConfirmation.innerHTML = jsonObj.template;
                                    contentPanelConfirmation.style.width = gScreenWidth + 'px';
                                    contentPanelConfirmation.style.display= 'block';

                                    // initialize the stage html elments
                                    initializeConfimation();

                                    var contentPanelPayment = document.getElementById('contentPanelPayment');
                                    contentPanelPayment.style.marginLeft = '-' + gScreenWidth + 'px';

                                    gActivePanel = 'showConfirmation';
                                break;

                                // slide to the confirmation page
                                case 'showPaymentgateway':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
                                        // change javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                        // show the payment gateway panel
                                        var contentPanelPaymentgateway = document.getElementById('contentPanelPaymentgateway');
                                        contentPanelPaymentgateway.innerHTML = jsonObj.template;
                                        contentPanelPaymentgateway.style.width = gScreenWidth + 'px';
                                        contentPanelPaymentgateway.style.display= 'block';

										if ((jsonObj.showerror == false) || (! jsonObj.hasOwnProperty('showerror')))
										{
											// initialize the stage html elments
											initializePaymentGateway();
										}
										else
										{
											closeLoadingDialog();
										}

										var contentPanelPayment = document.getElementById('contentPanelPayment');
                                        contentPanelPayment.style.marginLeft = '-' + gScreenWidth + 'px';

                                        gActivePanel = 'showPaymentgateway';
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
                                    }
                                break;

                                 // slide to the cancellation page
                                case 'showCancellation':

                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    document.getElementById('contentBlocCancel').innerHTML = jsonObj.template;

                                    setHashUrl('cancellation');

                                    gActivePanel = 'showCancellation';
                                    break;

                                case 'updateorderqtyall':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    var datas = jsonObj.data;
                                    var count = datas.length;
                                    var i = 0;
                                    var orderLinedata =  '';
                                    for (i = 0; i < count; i++)
                                    {
                                        document.getElementById(datas[i].content).innerHTML = unescape(decodeURIComponent(datas[i].orderLineHTML));

                                        orderLinedata = datas[i].data;

                                        // change the component panel
                                        var contentPanelComponent = document.getElementById('componentContainer' + orderLinedata.orderlineid);
                                        contentPanelComponent.innerHTML = unescape(decodeURIComponent(datas[i].componentDetailHTML));

                                        // change the subcomponent panel
                                        var contentPanelSubComponent = document.getElementById('subcomponentContainer' + orderLinedata.orderlineid);
                                        contentPanelSubComponent.innerHTML = unescape(decodeURIComponent(datas[i].subcomponentDetailHTML));

                                        var orderFooter = document.getElementById('orderFooter');
                                        if (orderFooter)
                                        {
                                            orderFooter.innerHTML = unescape(decodeURIComponent(datas[i].orderFooterHTML));
                                        }

                                        gOrderData[datas[i].data.orderlineid] = datas[i].data;
                                        gOrderCanContinue.ordercancontinue = datas[i].ordercancontinue;
                                        if (datas[i].vouchermessage != null && datas[i].vouchermessage.length > 0 )
                                        {
                                            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmation');?>
", datas[i].vouchermessage, function(e) {
                                                closeDialog(e);
                                            });
                                        }
                                    }

                                    //update order total
                                    document.getElementById("orderTotal").innerHTML = jsonObj.orderTotal;

                                    closeLoadingDialog();
                                break;

                                case 'setGiftCard':
                                case 'setVoucher':
                                    var jsonObj = parseJson(xmlhttp.responseText);

                                    if (jsonObj.result == true)
                                    {
										// change javascript
                                        toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                        var contentAjaxPayment = document.getElementById('contentAjaxPayment');
                                        contentAjaxPayment.innerHTML = jsonObj.template;

                                        // We need to include all the relevant script urls and scripts for each payment gateway.
                                        // This is to handle the situation where both PayPalPlus and another gateway that requires javascript are enabled.
                                        for (var paymentmethodCode in jsonObj.paymentgatewayjavascriptarray)
                                        {                                            
                                            var scriptURL = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('scripturl') ? jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].scripturl : '';
                                            var scriptForm = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('form') ? jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].form : '';
                                            var script = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('script') ? scriptForm + ' ' + jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].script : '';
                                            var integrity = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hasOwnProperty('hash') ? jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].hash : '';
                                            var requestPaymentParamsRemotely = jsonObj.paymentgatewayjavascriptarray[paymentmethodCode].requestpaymentparamsremotely;

                                            if (scriptURL !== '')
                                            {
                                                toggleJs('externalscript_' + paymentmethodCode, '', true, scriptURL, '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
', script, integrity);
                                            }

                                            if ((script !== '') && (scriptURL === ''))
                                            {
                                                toggleJs('paymentjavascript_' + paymentmethodCode, script, true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');
                                            }

											// Add the flag for lightbox payment gateways, this needs to be execute everytime the page is loaded.
											if (requestPaymentParamsRemotely)
											{
												toggleJs('requestparamsremotelyjavascript_' + paymentmethodCode, 'gRequestPaymentParamsRemotely = true;', true, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');
											}
                                        }

                                        // initialize the stage html elments
                                        initializeStage();
                                    }
                                    else
                                    {
                                        forceRedirectionToLogin(jsonObj.message);
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
                 * initializeCancelation
                 *
                 * set the size of the html elemnts on cancelation panel
                 */
                function initializeCancelation()
                {
                    setScrollAreaHeight('contentCancelation', '');

                    closeLoadingDialog();
                }

                function fnMetadataMethodClick(pElement)
                {
                    return metadataMethodClick(pElement, pElement.getAttribute('data-divid'));
                }

                /**
                 * metadataMethodClick
                 *
                 * Select the option clicked
                 */
                function metadataMethodClick(pObject, pDivId)
                {
                    /* loop through all the metadata to see which one has been selected */
                    var elem = pObject.parentNode.parentNode;
                    var inputs = elem.getElementsByTagName('input');

                    for (var i = 0; i < inputs.length; i++)
                    {
                        if (inputs[i].checked)
                        {
                            if (pDivId == '')
                            {
                                checkMetadataComponent(inputs[i]);
                            }
                            else
                            {
                                checkMetadataValidity(pDivId, false);
                            }
                            inputs[i].parentNode.classList.add('optionSelected');
                        }
                        else
                        {
                            inputs[i].parentNode.classList.remove('optionSelected');
                        }
                    }
                }

                // Wrapper for doneFromComponent function
                function fnDoneFromComponent(pElement)
                {
                    if (!pElement) {
                        return false;
                    }

                    return doneFromComponent(pElement.getAttribute('data-subcomponent'));
                }

                function doneFromComponent(pIsSubcomponent)
                {
                    var update = false;
                    var elementId = gComponentActive;
                    if (pIsSubcomponent)
                    {
                        elementId = gSubComponentActive;
                    }

                    var field = document.getElementById('itemqty_' + elementId);
                    var hideQty = document.getElementById('hiddeqty_' + elementId);
                    if ((field) && (hideQty))
                    {
                        var oldValue = hideQty.value;
                        if ((field.type == 'text') || (field.type == 'number'))
                        {
                            var newQty = string2integer(field.value);
                        }
                        else
                        {
                            var newQty = string2integer(field.options[field.selectedIndex].value);
                        }

                        if (newQty != oldValue)
                        {
                            gForceBack = true;
                            update = true;

                            if (gOrderlineidActive != '-1')
                            {
                                updateComponentQty(elementId, document.getElementById('itemqty_' + gOrderlineidActive).value, newQty);
                            }
                            else
                            {
                                updateComponentQty(elementId, 0, newQty);
                            }
                        }
                    }

                    if (! update)
                    {
                        if ((gActivePanel == 'contentPanelSubComponent') || (gActivePanel == 'contentPanelSubComponentBack'))
                        {
                            setHashUrl('componentView|' + gOrderlineidActive + '|' + gComponentActive);
                        }
                        else
                        {
                            setHashUrl('qty');
                        }
                    }
                }


/*** STAGE QTY ***/

                /**
                 * showPanelQty
                 *
                 * slide from a panel to another panel on qty stage
                 */
                function showPanelQty(pVisible, pDivID, pOrderLine, pComponentID)
                {
                    // main panel
                    if (pVisible)
                    {
                        // force the panel to be visible
                        document.getElementById(pDivID).style.display= 'block';

                        // slide to the panel
                        switch(pDivID)
                        {
                            case 'contentPanelComponent':

                                gScrollRefreshPosition = document.getElementById('contentLeftScrollQty').scrollTop;

                                // show the container of the componet for a line order
                                document.getElementById('componentContainer' + pOrderLine).style.display = 'block';

                                // show the component selected
                                document.getElementById('componentDetail_' + pComponentID).style.display = 'block';

                                // check if we are manipulating the order footer
                                if (pOrderLine == -1)
                                {
                                    setScrollAreaHeight('contentRightScrollOrderFooterComponentDetail', 'contentNavigationOrderFooterComponentDetail');
									document.getElementById('contentRightScrollOrderFooterComponentDetail').scrollTop  = 0;
                                }
                                else
                                {
                                    setScrollAreaHeight('contentRightScrollComponentDetail_' + pOrderLine, 'contentNavigationComponentDetail');
									document.getElementById('contentRightScrollComponentDetail_' + pOrderLine).scrollTop  = 0;
                                }

                                // resize the html elments
                                initializeComponentView('componentDetail_' + pComponentID);

                                // slide the qty panel
                                document.getElementById('contentPanelQty').style.marginLeft = '-' + gScreenWidth + 'px';

                                gComponentActive = pComponentID;
                                break;

                            case 'contentPanelSubComponent':
                            case 'contentPanelSubComponentBack':
								
								// check if the user is in the order footer to avoid a crash
								if (pOrderLine == -1)
								{
									gScrollPositionComponent = document.getElementById('contentRightScrollOrderFooterComponentDetail').scrollTop;
								}
								else
								{
									gScrollPositionComponent = document.getElementById('contentRightScrollComponentDetail_' + pOrderLine).scrollTop;
								}

                                // show the subcomponent
                                document.getElementById('contentPanelSubComponent').style.display = 'block';
                                document.getElementById('subcomponentContainer' + pOrderLine).style.display = 'block';
                                document.getElementById('subcomponentDetail_' + pComponentID).style.display = 'block';


                                // check if we are manipulating the order footer
                                if (pOrderLine == -1)
                                {
                                    setScrollAreaHeight('contentRightScrollOrderFooterSubComponentDetail', 'contentNavigationOrderFooterSubComponentDetail');
									document.getElementById('contentRightScrollOrderFooterSubComponentDetail').scrollTop  = 0;
                                }
                                else
                                {
                                    setScrollAreaHeight('contentRightScrollSubComponentDetail_' + pOrderLine, 'contentNavigationSubComponentDetail');
									document.getElementById('contentRightScrollSubComponentDetail_' + pOrderLine).scrollTop  = 0;
                                }

                                // resize the html elments
                                initializeComponentView('subcomponentDetail_' + pComponentID);

                                // slide the component panel
                                document.getElementById('contentPanelComponent').style.marginLeft = '-' + gScreenWidth + 'px';

                                gSubComponentActive = pComponentID;
                                break;

                            case 'contentPanelComponentChoice':

                                setScrollAreaHeight('contentRightScrollChoice', 'contentNavigationChoice');

                                // resize the html elments
                                initializeComponentChoice();

                                // detect which panel need to be slided
                                if ((gActivePanel == 'contentPanelComponent') || (gActivePanel == 'contentPanelComponentBack'))
                                {
                                    var contentPanelComponent = document.getElementById('contentPanelComponent');
                                    contentPanelComponent.style.marginLeft = '-' + gScreenWidth + 'px';
                                }
                                else
                                {								
                                    // check if the user is in the order footer to avoid a crash
									if (pOrderLine == -1)
									{
										gScrollPositionComponent = document.getElementById('contentRightScrollOrderFooterComponentDetail').scrollTop;
									}
									else
									{
										gScrollPositionComponent = document.getElementById('contentRightScrollComponentDetail_' + pOrderLine).scrollTop;
									}

                                    var contentPanelSubComponent = document.getElementById('contentPanelSubComponent');
                                    contentPanelSubComponent.style.marginLeft = '-' + gScreenWidth + 'px';
                                }

                                // close the loading box
                                closeLoadingDialog();

                                if (gActivePanel != 'contentPanelComponentChoice')
                                {
                                    gPreviousPanel = gActivePanel;
                                }
                            break;
                        }

                        gActivePanel = pDivID;
                        document.getElementById('contentPanelQty').style.display = 'block';

                        if (pOrderLine != '')
                        {
                            gOrderlineidActive = pOrderLine;
                        }
                    }
                    else
                    {
                        // slide back to a panel
                        switch(gActivePanel)
                        {
                            case 'contentPanelComponent':
                            case 'contentPanelComponentBack': // same as component panel but the application has be reloaded via ajax
                                if (gActivePanel == 'contentPanelComponentBack')
                                {
                                    // reset all design for qty panel
                                    initializeStage();
                                }

                                initializeQtyPanel();

                                document.getElementById('contentPanelQty').style.marginLeft = 0;

                                // delay to hide the panel to make sure the other panel is displayed (css animation)
                                setTimeout(function()
                                {
                                    // hide the component panels
                                    document.getElementById('componentContainer' + gOrderlineidActive).style.display = 'none';
                                    document.getElementById('componentDetail_' + gComponentActive).style.display = 'none';
                                    gComponentActive = '';

                                    // reset the active panel
                                    gActivePanel = '';
                                }, 300);

                            break;

                            case 'contentPanelSubComponent':

                                document.getElementById('subcomponentContainer' + gOrderlineidActive).style.display = 'none';
                                document.getElementById('subcomponentDetail_' + gSubComponentActive).style.display = 'none';

								if (gOrderlineidActive == -1)
                                {
                                    setScrollAreaHeight('contentRightScrollOrderFooterComponentDetail', 'contentNavigationOrderFooterComponentDetail');
                                }
                                else
                                {
                                    setScrollAreaHeight('contentRightScrollComponentDetail_' + gOrderlineidActive, 'contentNavigationComponentDetail');
                                }

                                // force to resize the main page and component
                                initializeComponentView('componentDetail_' + gComponentActive);
								
								//if in order footer scroll to top of order footer otherwise scroll to product
								if (gOrderlineidActive == -1)
								{  
									document.getElementById('contentRightScrollOrderFooterComponentDetail').scrollTop = gScrollPositionComponent;
								}
								else
								{
									document.getElementById('contentRightScrollComponentDetail_' + gOrderlineidActive).scrollTop = gScrollPositionComponent;
								}
								
                                gScrollPositionComponent = 0;

                                document.getElementById('contentPanelComponent').style.marginLeft = 0;

                                gActivePanel = 'contentPanelComponent';

                                break;

                            case 'contentPanelSubComponentBack':

                                // hide the subcomponent panels
                                document.getElementById('subcomponentContainer' + gOrderlineidActive).style.display = 'none';
                                document.getElementById('subcomponentDetail_' + gSubComponentActive).style.display = 'none';

                                // show components panels
                                document.getElementById('componentContainer' + gOrderlineidActive).style.display = 'block';
                                document.getElementById('componentDetail_' + gComponentActive).style.display = 'block';

                                // check if we are manipulating
                                if (gOrderlineidActive == -1)
                                {
                                    setScrollAreaHeight('contentRightScrollOrderFooterComponentDetail', 'contentNavigationOrderFooterComponentDetail');
                                }
                                else
                                {
                                    setScrollAreaHeight('contentRightScrollComponentDetail_' + gOrderlineidActive, 'contentNavigationComponentDetail');
                                }

                                // force to resize the main page and component
                                initializeStage();
                                initializeComponentView('componentDetail_' + gComponentActive);
								
								// check if user is accessing the order footer
								if ((pOrderLine === -1) || (pOrderLine == ''))
								{
									// force the scroll position for components
									document.getElementById('contentRightScrollOrderFooterComponentDetail').scrollTop = gScrollPositionComponent;
								}
								else
								{
									// force the scroll position for components
									document.getElementById('contentRightScrollComponentDetail_' + gOrderlineidActive).scrollTop = gScrollPositionComponent;
								}		
								gScrollPositionComponent = 0;

                                // slide the component panel
                                document.getElementById('contentPanelComponent').style.marginLeft = 0;

                                // reset the active panel
                                gActivePanel = 'contentPanelComponent';

                                gSubComponentActive = '';

                                break;

                            case 'contentPanelComponentChoice':

                                // check if we come from component or subcomponent
                                if ((gPreviousPanel == 'contentPanelComponent') || (gPreviousPanel == 'contentPanelComponentBack'))
                                {
                                    initializeComponentView('componentDetail_' + gComponentActive);

                                    var contentPanelComponent = document.getElementById('contentPanelComponent');
                                    contentPanelComponent.style.marginLeft = 0;
                                    gActivePanel = 'contentPanelComponentBack';
                                }
                                else
                                {
                                    initializeComponentView('subcomponentDetail_' + gSubComponentActive);

                                    var contentPanelSubComponent = document.getElementById('contentPanelSubComponent');
                                    contentPanelSubComponent.style.marginLeft = 0;
                                    gActivePanel = 'contentPanelSubComponentBack';
                                }

                                // hide the choice panel
                                document.getElementById('contentPanelComponentChoice').style.display = 'none';

                            break;
                        }
                    }

                }

                function fnCheckboxEffectAction(pElement)
                {
                    return checkboxEffectAction(pElement.getAttribute('data-orderlineid'), pElement.getAttribute('data-checkboxlineid'));
                }

                /**
                 * checkboxEffectAction
                 *
                 * Apply the effect for the checkbox button and trigger an ajax call to update the panel
                 */
                function checkboxEffectAction(pItemID, pCheckboxID)
                {
                    if (gActivePanel == '')
                    {
                        gScrollRefreshPosition = document.getElementById('contentLeftScrollQty').scrollTop;
                    }
                    else
                    {
                        gScrollRefreshPosition = document.getElementById('contentRightScrollComponentDetail_' + gOrderlineidActive).scrollTop;
                    }

                    var element = document.getElementById('checkbox_' + pCheckboxID);

                    if (document.getElementById('onOffSwitch_' + pCheckboxID).checked)
                    {
                        element.style.right = '2px';
                    }
                    else
                    {
                        element.style.right = '24px';
                    }

                    updateCheckbox(pItemID, pCheckboxID);
                }

                /**
                 * setMetadataDesign
                 *
                 * Set the metadata design
                 */
                function setMetadataDesign(pDivContainerActive)
                {
                    var blocWidth = gInnerBoxContentBloc;

                    /** metadata design **/
                    var containerActive = document.getElementById(pDivContainerActive);
                    var metadata = containerActive.getElementsByClassName('metadatacontent')[0];
                    if (metadata)
                    {
                        var widthInputText = 0;
                        var widthInputRadioNoPreview = 0;
                        var widthInputRadioPreview = 0;

                        var metadataLegnth = containerActive.getElementsByClassName('componentMetadata').length;
                        for (var i = 0; i < metadataLegnth; i++)
                        {
                            var componentMetadata = containerActive.getElementsByClassName('componentMetadata')[i];
                            var inputs = componentMetadata.getElementsByTagName('input');
                            var textareas = componentMetadata.getElementsByTagName('textarea');

                            /* get metadata values for all inputs on the page */
                            for (var j = 0; j < inputs.length; j++)
                            {
                                switch (inputs[j].type)
                                {
                                    case 'text':

                                        if (widthInputText == 0)
                                        {
                                            var styleInputText = inputs[j].currentStyle || window.getComputedStyle(inputs[j]);
                                            widthInputText = blocWidth - parseIntStyle(styleInputText.borderLeftWidth) - parseIntStyle(styleInputText.borderRightWidth);
                                            widthInputText = widthInputText - parseIntStyle(styleInputText.paddingLeft) - parseIntStyle(styleInputText.paddingRight);
                                            widthInputText = widthInputText - parseIntStyle(styleInputText.marginLeft) - parseIntStyle(styleInputText.marginRight);
                                        }

                                        inputs[j].style.width = widthInputText + 'px';
                                        break;
                                    case 'radio':

                                        if (widthInputRadioNoPreview == 0)
                                        {
                                            // tick image width
                                            if (gTickImageWidth == 0)
                                            {
                                                var checkboxImage = containerActive.getElementsByClassName('checkboxImage')[0];
                                                var styleCheckboxImage = checkboxImage.currentStyle || window.getComputedStyle(checkboxImage);
                                                gTickImageWidth =  parseIntStyle(styleCheckboxImage.width) + parseIntStyle(styleCheckboxImage.marginLeft) + parseIntStyle(styleCheckboxImage.marginRight);
                                            }

                                            widthInputRadioNoPreview = blocWidth - gTickImageWidth;

                                            var radioImage = containerActive.getElementsByClassName('radioImage')[0];
                                            if (radioImage)
                                            {
                                                var styleRadioImage = radioImage.currentStyle || window.getComputedStyle(radioImage);
                                                widthInputRadioPreview = widthInputRadioNoPreview - parseIntStyle(styleRadioImage.width)
                                                widthInputRadioPreview = widthInputRadioPreview - parseIntStyle(styleRadioImage.marginLeft) - parseIntStyle(styleRadioImage.marginRight);
                                            }
                                        }

                                        var classLength = containerActive.getElementsByClassName('listLabel').length;
                                        for (var i = 0; i < classLength; i++)
                                        {
                                            var elm = containerActive.getElementsByClassName('listLabel')[i];
                                            var nextElem = elm.previousSibling;

                                            if (i == 0)
                                            {
                                                var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                                                widthInputRadioNoPreview = widthInputRadioNoPreview - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                                                widthInputRadioPreview = widthInputRadioPreview - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                                            }

                                            //detect if an image is displayed
                                            if (nextElem.className.indexOf("radioImage") != -1)
                                            {
                                                //check if parent is highlighted
                                                if (elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
                                                {
                                                    elm.style.width = (widthInputRadioPreview - gHighLightBorderSizeDifference) + 'px';
                                                }
                                                else
                                                {
                                                    elm.style.width = widthInputRadioPreview + 'px';
                                                }
                                            }
                                            else
                                            {
                                                //check if parent is highlighted
                                                if (elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
                                                {
                                                    elm.style.width = (widthInputRadioNoPreview - gHighLightBorderSizeDifference) + 'px';
                                                }
                                                else
                                                {
                                                    elm.style.width = widthInputRadioNoPreview + 'px';
                                                }
                                            }
                                        }
                                        break;
                                    case 'checkbox':
                                        var checkboxWidth = blocWidth;

                                        var classLength = containerActive.getElementsByClassName('metadataCheckboxLabel').length;
                                        for (var i = 0; i < classLength; i++)
                                        {
                                            var elm = containerActive.getElementsByClassName('metadataCheckboxLabel')[i];
                                            if (i == 0)
                                            {
                                                var inputCheckbox = containerActive.getElementsByClassName('inputCheckbox')[0];
                                                var styleInputCheckbox = inputCheckbox.currentStyle || window.getComputedStyle(inputCheckbox);
                                                checkboxWidth = checkboxWidth - parseIntStyle(styleInputCheckbox.width)
                                                checkboxWidth = checkboxWidth - parseIntStyle(styleInputCheckbox.marginLeft) - parseIntStyle(styleInputCheckbox.marginRight);

                                                var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                                                checkboxWidth = checkboxWidth - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                                                checkboxWidth = checkboxWidth - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                                            }
                                            elm.style.width = checkboxWidth + 'px';
                                        }
                                        break;
                                }
                            }

                            /* get metadata values for all textareas on the page */
                            var widthTextarea = 0;
                            for (var j = 0; j < textareas.length; j++)
                            {
                                if (widthTextarea == 0)
                                {
                                    var styleTextarea = textareas[j].currentStyle || window.getComputedStyle(textareas[j]);
                                    widthTextarea = blocWidth - parseIntStyle(styleTextarea.borderLeftWidth) - parseIntStyle(styleTextarea.borderRightWidth);
                                    widthTextarea = widthTextarea - parseIntStyle(styleTextarea.paddingLeft) - parseIntStyle(styleTextarea.paddingRight);
                                    widthTextarea = widthTextarea - parseIntStyle(styleTextarea.marginLeft) - parseIntStyle(styleTextarea.marginRight);
                                }

                                textareas[j].style.width = widthTextarea + 'px';
                            }
                        }
                    }

                    /** end metadata design **/
                }

                function fnCheckMetadataComponent(pElement, pEvent)
                {
                    return checkMetadataComponent(pElement);
                }

                /**
                 * checkMetadataComponent
                 *
                 * Check the validity of the metadata value, add or remove the highlight effect to a metadata box and the parents if no othor errors exists.
                 */
                function checkMetadataComponent(pObject)
                {
                    var componentHighLightBloc = '';
                    var radios = [];
                    var iDsMetadataContainer = [];
                    var highlightBoxes = [];
                    var hasAnError= false;
                    var componentBlocId = '';
                    var subcomponentBlocId = '';
					var subcomponentDetailId = '';
                    var metadatContainerId = -1;
                    var isSubcomponent = false;

                    // get metadata values for all inputs on the page
                    switch (pObject.type)
                    {
                        case 'text':
                            componentHighLightBloc = pObject.parentNode.parentNode.parentNode.parentNode.parentNode;
                            if ((pObject.className.indexOf('required') > -1) && (pObject.value == ''))
                            {
                                hasAnError = true;
                            }
                            break;

                        case 'radio':
                            if( !(Object.prototype.toString.call(radios[pObject.name]) === '[object Array]' ))
                            {
                                radios[pObject.name] = [];
                            }
                            radios[pObject.name].push(pObject);
                            break;
                        case 'textarea':
                            componentHighLightBloc = pObject.parentNode.parentNode.parentNode.parentNode.parentNode;
                            if ((pObject.className.indexOf('required') > -1) && (pObject.value == ''))
                            {
                                hasAnError = true;
                            }
                            break;
                        case 'select-one':
                            componentHighLightBloc = pObject.parentNode.parentNode.parentNode.parentNode.parentNode;
                            if (pObject.options[pObject.selectedIndex].value == '')
                            {
                                hasAnError = true;
                            }
                            break;
                    }

                    for (var radio in radios)
                    {
                        var checked = false;
                        for (var x=0; x < radios[radio].length; x++)
                        {
                            if (radios[radio][x].checked)
                            {
                                checked = true;
                            }
                        }

                        if(checked == false)
                        {
                            hasAnError = true;
                        }

                        x = x -1;

                        componentHighLightBloc = radios[radio][x].parentNode.parentNode.parentNode.parentNode.parentNode;
                    }

                    var objectID = componentHighLightBloc.className.replace('componentMetadata outerBoxPadding metadataId', '');
                    var objectIDSplit = objectID.split("_");

                    // detect if the object is associted to a component or a subcomponent
                    if (objectIDSplit.length == 2)
                    {
                        componentBlocId = 'componentContent_' + objectID;
                        componentId = objectID;
						metadatContainerId = 'componentDetail_' + componentId;
                    }
                    else
                    {
                        // add component and subcomponent
                        componentBlocId = 'componentContent_' + objectIDSplit[0] + '_' + objectIDSplit[1];
                        componentId = objectIDSplit[0] + '_' + objectIDSplit[1];
                        subcomponentBlocId = 'componentContent_' + objectID;
						metadatContainerId = 'subcomponentDetail_' + objectIDSplit[2] + '_' + objectIDSplit[3];
                    }

					iDsMetadataContainer.push(metadatContainerId);
					if (subcomponentBlocId == '')
					{
						// get all metadatas container
						var componentDetail = document.getElementById('componentDetail_' + componentId);
						var subComponentLength = componentDetail.getElementsByClassName('contentSubComponentBloc').length;
						for (var i = 0; i < subComponentLength; i++)
						{
							var subComponent = componentDetail.getElementsByClassName('contentSubComponentBloc')[i];
							iDsMetadataContainer.push(subComponent.id.replace('componentContent_' + componentId, 'subcomponentDetail'));
						}
					}

                    // loop through all metadatas container and check if one of metadata is highlighted
                    var otherError = false;
                    var idsLength = iDsMetadataContainer.length;
                    for (var idIndex= 0; idIndex < idsLength; idIndex++)
                    {
                        var contentMetadata = document.getElementById(iDsMetadataContainer[idIndex]);
                        if (contentMetadata)
                        {
                            var metadataLegnth = contentMetadata.getElementsByClassName('componentMetadata').length;

                            for (var i = 0; i < metadataLegnth; i++)
                            {
                                var componentMetadata = contentMetadata.getElementsByClassName('componentMetadata')[i];
                                var inputs = componentMetadata.getElementsByTagName('input');
                                var selects = componentMetadata.getElementsByTagName('select');
                                var textareas = componentMetadata.getElementsByTagName('textarea');

                                /* get metadata values for all inputs on the page */
                                for (var j = 0; j < inputs.length; j++)
                                {
									if (inputs[j] != pObject)
									{
										switch (inputs[j].type)
										{
											case 'text':
												// check if the other box is already highlighted
												if (inputs[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
												{
													if ((inputs[j].className.indexOf('required') > -1) && (inputs[j].value == ''))
													{
														otherError = true;

														var componentHighLight = inputs[j].parentNode.parentNode.parentNode.parentNode.parentNode;
														var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
														var componentIDs = componentID.split("_");

														if (componentIDs.length == 2)
														{
															highlightBoxes.push('componentContent_' + componentID);
														}
														else
														{
															// add component and subcomponent
															highlightBoxes.push('componentContent_' +  componentIDs[0] + '_' + componentIDs[1]);
															highlightBoxes.push('componentContent_' + componentID);
															isSubcomponent = true;
														}
													}
												}

												break;
											case 'radio':
												// check if the other box is already highlighted
												if (inputs[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
												{
													if( !(Object.prototype.toString.call(radios[inputs[j].name]) === '[object Array]' ))
													{
														radios[inputs[j].name] = [];
													}
													radios[inputs[j].name].push(inputs[j]);
												}
												break;
											case 'checkbox':
												break;
										}
									}
                                }

                                for(var radio in radios)
                                {
                                    var checked = false;
                                    for (var x=0; x < radios[radio].length; x++)
                                    {
                                        if (radios[radio][x].checked)
                                        {
                                            checked = true;
                                        }
                                    }

                                    if(checked == false)
                                    {
                                        x = x -1;

                                        otherError = true;

                                        var componentHighLight = radios[radio][x].parentNode.parentNode.parentNode.parentNode.parentNode;
                                        var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
                                        var componentIDs = componentID.split("_");
                                        if (componentIDs.length == 2)
                                        {
                                            highlightBoxes.push('componentContent_' + componentID);
                                        }
                                        else
                                        {
                                            // add component and subcomponent
                                            highlightBoxes.push('componentContent_' + componentIDs[0] + '_' + componentIDs[1]);
                                            highlightBoxes.push('componentContent_' + componentID);
                                            isSubcomponent = true;
                                        }
                                    }
                                }

                                /* get metadata values for all selects on the page */
                                for (var j = 0; j < selects.length; j++)
                                {
									// check if the other box is already highlighted
									if (selects[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
									{
										if (selects[j].options[selects[j].selectedIndex].value == '')
										{
											otherError = true;

											var componentHighLight = selects[j].parentNode.parentNode.parentNode.parentNode.parentNode;
											var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
											var componentIDs = componentID.split("_");

											if (componentIDs.length == 2)
											{
												highlightBoxes.push('componentContent_' + componentID);
											}
											else
											{
												// add component and subcomponent
												highlightBoxes.push('componentContent_' + componentIDs[0] + '_' + componentIDs[1]);
												highlightBoxes.push('componentContent_' + componentID);
												isSubcomponent = true;
											}

										}
									}
                                }

                                /* get metadata values for all textareas on the page */
                                for (var j = 0; j < textareas.length; j++)
                                {
									// check if the other box is already highlighted
									if (textareas[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
									{
										if ((textareas[j].className.indexOf('required') > -1) && (textareas[j].value == ''))
										{
											otherError = true;

											componentHighLight = textareas[j].parentNode.parentNode.parentNode.parentNode.parentNode;
											var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
											var componentIDs = componentID.split("_");

											if (componentIDs.length == 2)
											{
												highlightBoxes.push('componentContent_' + componentID);
											}
											else
											{
												// add component and subcomponent
												highlightBoxes.push('componentContent_' + componentIDs[0] + '_' + componentIDs[1]);
												highlightBoxes.push('componentContent_' + componentID);
												isSubcomponent = true;
											}
										}
									}
                                }
                            }
                        }

                    }

                    // if no error we remove the selection
                    if ((otherError == false) && (hasAnError == false))
                    {
						var clearComponent = true;

                        //add subcomponent if needed
                        if (subcomponentBlocId != '')
                        {
							var componentDetail = document.getElementById('componentDetail_' + componentId);
							var subComponentLength = componentDetail.getElementsByClassName('contentSubComponentBloc').length;
							for (var i = 0; i < subComponentLength; i++)
							{
								var subComponent = componentDetail.getElementsByClassName('contentSubComponentBloc')[i];

								if (subComponent.id == subcomponentBlocId)
								{
									highlightBoxes.push(subComponent.id);
								}
								else
								{
									if (subComponent.className.indexOf('componentHighLight') > -1)
									{
										clearComponent = false;
									}
								}
							}

							if (clearComponent)
							{
								// me sure all metadat attached to the component are correct
								var contentMetadata = document.getElementById('componentDetail_' + componentId);
								if (contentMetadata)
								{
									var metadataLegnth = contentMetadata.getElementsByClassName('componentMetadata').length;

									for (var i = 0; i < metadataLegnth; i++)
									{
										var componentMetadata = contentMetadata.getElementsByClassName('componentMetadata')[i];
										var inputs = componentMetadata.getElementsByTagName('input');
										var selects = componentMetadata.getElementsByTagName('select');
										var textareas = componentMetadata.getElementsByTagName('textarea');

										/* get metadata values for all inputs on the page */
										for (var j = 0; j < inputs.length; j++)
										{
											if (inputs[j] != pObject)
											{
												switch (inputs[j].type)
												{
													case 'text':
														// check if the other box is already highlighted
														if (inputs[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
														{
															clearComponent = false;
														}
														break;
													case 'radio':
														// check if the other box is already highlighted
														if (inputs[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
														{
															clearComponent = false;
														}
														break;
													case 'checkbox':
														break;
												}
											}
										}

										/* get metadata values for all selects on the page */
										for (var j = 0; j < selects.length; j++)
										{
											// check if the other box is already highlighted
											if (selects[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
											{
												clearComponent = false;
											}
										}

										/* get metadata values for all textareas on the page */
										for (var j = 0; j < textareas.length; j++)
										{
											// check if the other box is already highlighted
											if (textareas[j].parentNode.parentNode.parentNode.className.indexOf('componentHighLight') > -1)
											{
												clearComponent = false;
											}
										}
									}
								}
							}
                        }

						if (clearComponent)
						{
							// add the component
							highlightBoxes.push(componentBlocId);
						}

                        // remove my object highlighted
                        highlightBoxes.push('metadataItem' +  pObject.name);

                        // remove highlights
                        setHighlightBox(highlightBoxes, false);
                    }
                    else
                    {
                        if (otherError == true)
                        {
                            if (hasAnError == true)
                            {
                                // add my object highlighted
                                highlightBoxes.push('metadataItem' +  pObject.name);

								highlightBoxes.push(componentBlocId);

								if ((isSubcomponent == true) && (subcomponentBlocId != ''))
								{
									// add subcomponent my object highlighted
									highlightBoxes.push(subcomponentBlocId);
								}

								// add highlights
								setHighlightBox(highlightBoxes, true);
                            }
							else
							{
								var objectToremove = [];

								// remove my object highlighted
								objectToremove.push('metadataItem' +  pObject.name);

								objectToremove.push(componentBlocId);

								if ((isSubcomponent == true) && (subcomponentBlocId != ''))
								{
									// add subcomponent my object highlighted
									objectToremove.push(subcomponentBlocId);
								}

								// remove highlights
								setHighlightBox(objectToremove, false);

								// add highlights
								setHighlightBox(highlightBoxes, true);
							}
                        }
						else
						{
							// add my object highlighted
							highlightBoxes.push('metadataItem' +  pObject.name);

							highlightBoxes.push(componentBlocId);

							if (subcomponentBlocId != '')
							{
								// add subcomponent my object highlighted
								highlightBoxes.push(subcomponentBlocId);
							}

							// add highlights
							setHighlightBox(highlightBoxes, true);
						}
                    }
                }

                /**
                 * setHighlightAllBoxes
                 *
                 * Add or remove the highlight effect to a metadata boxes and the parents if an error occured.
                 */
                function setHighlightAllBoxes(pHighLightBoxes)
                {
                    var i = 0;
                    var outerPage = document.getElementById('outerPage');
                    var componentHighLighted = outerPage.getElementsByClassName("componentHighLight");
                    var countComponentHighLighted = componentHighLighted.length;

                    // loop through all highlighted divs to remove the class, componentHilighted is an html collection
                    // each time the class is removed the collection update, this is why i is not use inside the loop and only the first element (0) is updated
                    for (i = 0; i < countComponentHighLighted; i++)
                    {
                        if (typeof(componentHighLighted[0]) != 'undefined')
                        {
                            var componentContentText = componentHighLighted[0].getElementsByClassName('componentContentText')[0];
                            if (componentContentText)
                            {
                                componentContentText.style.width = (parseIntStyle(componentContentText.style.width) + gHighLightBorderSizeDifference) + 'px';
                            }
                            else
                            {
                                var componentContentTextLong = componentHighLighted[0].getElementsByClassName('componentContentTextLong')[0];
                                if (componentContentText)
                                {
                                    componentContentTextLong.style.width = (parseIntStyle(componentContentTextLong.style.width) + gHighLightBorderSizeDifference) + 'px';
                                }
                                else
                                {
                                    var radioLength = componentHighLighted[0].getElementsByClassName('listLabel').length;
                                    for (var j = 0; j < radioLength; j++)
                                    {
                                        var listLabel = componentHighLighted[0].getElementsByClassName('listLabel')[j];
                                        listLabel.style.width = (parseIntStyle(listLabel.style.width) + gHighLightBorderSizeDifference) + 'px';

                                    }
                                }
                            }

							var componentLabel =  componentHighLighted[0].getElementsByClassName('componentLabel')[0];
							if (componentLabel)
                            {
                                componentLabel.style.width = (parseIntStyle(componentLabel.style.width) + gHighLightBorderSizeDifference) + 'px';
                            }

                            componentHighLighted[0].className = componentHighLighted[0].className.replace(' componentHighLight', '');
                        }
                    }

                    // loop through all divs to add the highlight class.
                    var uniqueValueArray = [];
                    for (i = 0; i < pHighLightBoxes.length; i++)
                    {
                        uniqueValueArray[componentID] = '';
                        var componentID = pHighLightBoxes[i];
                        if (typeof(uniqueValueArray[componentID]) == 'undefined')
                        {
                            var elm = document.getElementById(componentID);
                            elm.className = elm.className + ' componentHighLight';

                            var componentContentText = elm.getElementsByClassName('componentContentText')[0];
                            if (componentContentText)
                            {
                                componentContentText.style.width = (parseIntStyle(componentContentText.style.width) - gHighLightBorderSizeDifference) + 'px';
                            }
                            else
                            {
                                var componentContentTextLong = elm.getElementsByClassName('componentContentTextLong')[0];
                                if (componentContentTextLong)
                                {
                                    componentContentTextLong.style.width = (parseIntStyle(componentContentTextLong.style.width) - gHighLightBorderSizeDifference) + 'px';
                                }
								else
                                {
                                    var radioLength = componentHighLighted[0].getElementsByClassName('listLabel').length;
                                    for (var j = 0; j < radioLength; j++)
                                    {
                                        var listLabel = componentHighLighted[0].getElementsByClassName('listLabel')[j];
                                        listLabel.style.width = (parseIntStyle(listLabel.style.width) - gHighLightBorderSizeDifference) + 'px';
                                    }
                                }
                            }

							var componentLabel = elm.getElementsByClassName('componentLabel')[0];
							if (componentLabel)
                            {
                                componentLabel.style.width = (parseIntStyle(componentLabel.style.width) - gHighLightBorderSizeDifference) + 'px';
                            }
                        }
                    }
                }

                /**
                 * setHighlightBox
                 *
                 * Add or remove the highlight effect to a metadata boxes and parents for a specific boxes.
                 */
                function setHighlightBox(pHighLightBoxes, pAddClass)
                {
                    var i = 0;

                    // loop through all divs to add the highlight class.
                    var uniqueValueArray = [];
                    for (i = 0; i < pHighLightBoxes.length; i++)
                    {
                        uniqueValueArray[componentID] = '';
                        var componentID = pHighLightBoxes[i];
                        if (typeof(uniqueValueArray[componentID]) == 'undefined')
                        {
                            var elm = document.getElementById(componentID);
                            if (pAddClass == true)
                            {
                                elm.className = elm.className.replace(' componentHighLight', '') + ' componentHighLight';

                                var componentContentText = elm.getElementsByClassName('componentContentText')[0];
                                if (componentContentText)
                                {
                                    componentContentText.style.width = (parseIntStyle(componentContentText.style.width) - gHighLightBorderSizeDifference) + 'px';
                                }
                                else
                                {
                                    var componentContentTextLong = elm.getElementsByClassName('componentContentTextLong')[0];
                                    if (componentContentTextLong)
                                    {
                                        componentContentTextLong.style.width = (parseIntStyle(componentContentTextLong.style.width) - gHighLightBorderSizeDifference) + 'px';
                                    }
                                }

								var componentLabel = elm.getElementsByClassName('componentLabel')[0];
								if (componentLabel)
								{
									componentLabel.style.width = (parseIntStyle(componentLabel.style.width) - gHighLightBorderSizeDifference) + 'px';
								}
                            }
                            else
                            {
                                if (elm.className.indexOf('componentHighLight') > -1)
                                {
                                    var componentContentText = elm.getElementsByClassName('componentContentText')[0];
                                    if (componentContentText)
                                    {
                                        componentContentText.style.width = (parseIntStyle(componentContentText.style.width) + gHighLightBorderSizeDifference) + 'px';
                                    }
                                    else
                                    {
                                        var componentContentTextLong = elm.getElementsByClassName('componentContentTextLong')[0];
                                        if (componentContentTextLong)
                                        {
                                            componentContentTextLong.style.width = (parseIntStyle(componentContentTextLong.style.width) + gHighLightBorderSizeDifference) + 'px';
                                        }
                                        else
                                        {
                                            var radioLength = elm.getElementsByClassName('listLabel').length;
                                            for (var j = 0; j < radioLength; j++)
                                            {
                                                var listLabel = elm.getElementsByClassName('listLabel')[j];
                                                listLabel.style.width = (parseIntStyle(listLabel.style.width) + gHighLightBorderSizeDifference) + 'px';
                                            }
                                        }
                                    }

									var componentLabel = elm.getElementsByClassName('componentLabel')[0];
									if (componentLabel)
									{
										componentLabel.style.width = (parseIntStyle(componentLabel.style.width) + gHighLightBorderSizeDifference) + 'px';
									}

                                    elm.className = elm.className.replace(' componentHighLight', '');
                                }
                            }
                        }
                    }
                }

                // wrapper for changeSystemLanguageSmallScreen
                function fnChangeSystemLanguageSmallScreen(pElement)
                {
                    if (!pElement) {
                        return false;
                    }

                    return changeSystemLanguageSmallScreen(pElement.getAttribute('data-code'));
                }

/*** STAGE SHIPPING ***/

                /**
                 * showPanelShipping
                 *
                 * slide from a panel to another panel
                 */
                function showPanelShipping(pVisible, pDivID)
                {
                    // main panel
                    var contentPanelShipping = document.getElementById('contentPanelShipping');

                    if (pVisible)
                    {
                        // force the panel tio be visible
                        document.getElementById(pDivID).style.display= 'block';

                        switch(pDivID)
                        {
                            case 'contentPanelMethodList':

                                // set the height of the current panel
                                setScrollAreaHeight('contentRightScrollMethodList', 'contentNavigationMethodList');

                                // size of container
                                var container = document.getElementById('contentRightScrollMethodList');
                                var contentClick = container.getElementsByClassName('shippingContentClick')[0];
                                if (contentClick)
                                {
                                    // tick image width
                                    if (gTickImageWidth == 0 )
                                    {
                                        var checkboxImage = container.getElementsByClassName('checkboxImage')[0];
                                        var styleCheckboxImage = checkboxImage.currentStyle || window.getComputedStyle(checkboxImage);
                                        gTickImageWidth =  parseIntStyle(styleCheckboxImage.width) + parseIntStyle(styleCheckboxImage.marginLeft) + parseIntStyle(styleCheckboxImage.marginRight);
                                    }

                                    var widthContent = gOuterBoxContentBloc - gTickImageWidth;

                                    // size of the arrow for collect from store
                                    var contentArrow = container.getElementsByClassName('btnCollectFromStore')[0];
                                    var widthArrow = 0;
                                    if (contentArrow)
                                    {
                                        var styleArrow = contentArrow.currentStyle || window.getComputedStyle(contentArrow);
                                        widthArrow = parseIntStyle(styleArrow.width) + parseIntStyle(styleArrow.marginLeft) + parseIntStyle(styleArrow.marginRight);
                                    }

                                    var classLength = container.getElementsByClassName('listLabel').length;
                                    for (var i = 0; i < classLength; i++)
                                    {
                                        var elm = container.getElementsByClassName('listLabel')[i];

                                        if (i == 0)
                                        {
                                            var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                                            widthContent = widthContent - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                                        }

                                        elm.style.width = (widthContent - widthArrow) + 'px';
                                    }
                                }

                                contentPanelShipping.style.marginLeft = '-' + gScreenWidth + 'px';

                                setTimeout(function(){
                                    contentPanelShipping.style.display = 'block';
                                }, 300);

                            break;
                            case 'contentPanelSelectStore':

                                gScrollPositionShipping = document.getElementById('contentLeftScrollShipping').scrollTop;

                                if (gActivePanel == 'contentPanelMethodList')
                                {
                                    document.getElementById('contentPanelMethodList').style.marginLeft = '-' + gScreenWidth + 'px';
                                }
                                else
                                {
                                    document.getElementById('contentPanelMethodList').style.display = 'none';
                                    contentPanelShipping.style.marginLeft = '-' + gScreenWidth + 'px';
                                }

                                // Correct the logo width
                                var storeLogo = document.getElementById('storeLogoImg');
                                if (storeLogo)
                                {
                                    storeLogo.style.maxWidth = gOuterBoxContentBloc + 'px';
                                }

                                setTimeout(function(){
                                    document.getElementById('contentPanelQty').style.display = "block";
                                    contentPanelShipping.style.display = 'block';
                                    // fix the size of the container for scrollbar option
                                    setScrollAreaHeight( 'contentStoreList', 'contentNavigationStore');
                                    // close loading dialog
                                    closeLoadingDialog();
                                }, 300);

                            break;
                            case 'contentPanelUpdateAddress':
                                gScrollPositionShipping = document.getElementById('contentLeftScrollShipping').scrollTop;

                                setScrollAreaHeight('contentRightScrollAddress', 'contentNavigationAddress');

                                if (gActivePanel == 'contentPanelMethodList')
                                {
                                    document.getElementById('contentPanelMethodList').style.marginLeft = '-' + gScreenWidth + 'px';
                                }
                                else
                                {
                                    document.getElementById('contentPanelMethodList').style.display = 'none';
                                    contentPanelShipping.style.marginLeft = '-' + gScreenWidth + 'px';
                                }

                                setTimeout(function(){
                                    contentPanelShipping.style.display = 'block';
                                }, 300);
                            break;
                        }

                        gActivePanel = pDivID;
                    }
                    else
                    {
                        switch(gActivePanel)
                        {
                            case 'contentPanelMethodList':
                                document.getElementById('contentLeftScrollShipping').scrollTop = gScrollPositionShipping;

                                contentPanelShipping.style.marginLeft = 0;

                                setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');

                                gActivePanel = 'contentPanelShipping';
                            break;
                            case 'contentPanelSelectStore':

                                toggleJs('includeSelectStoreJS', '', false, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                var contentPanelMethodList = document.getElementById('contentPanelMethodList');

                                if (gPreviousHash == '#changeMethod')
                                {
                                    contentPanelMethodList.style.marginLeft = 0;
                                    contentPanelMethodList.style.display = 'block';

                                    gActivePanel = 'contentPanelMethodList';

									// close the loading box
									closeLoadingDialog();
                                }
                                else
                                {
                                    // set the content width
                                    initializeStage();

									setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');

                                    document.getElementById('contentLeftScrollShipping').scrollTop = gScrollPositionShipping;

                                    contentPanelShipping.style.marginLeft = 0;

                                    gActivePanel = 'contentPanelShipping';
                                }

                                setScrollAreaHeight('contentRightScrollMethodList', 'methodBack');

                                document.getElementById('contentPanelSelectStore').style.display = 'none';
                            break;
                            case 'contentPanelUpdateAddress':

                                toggleJs('includeUpdateAddressJS', '', false, '', '<?php echo $_smarty_tpl->tpl_vars['nonceraw']->value;?>
');

                                var contentPanelMethodList = document.getElementById('contentPanelMethodList');

                                if (parseIntStyle(contentPanelMethodList.style.marginLeft) < 0)
                                {
                                    contentPanelMethodList.style.marginLeft = 0;
                                    contentPanelMethodList.style.display = 'block';

                                    // close loading dialog
                                    closeLoadingDialog();

                                    gActivePanel = 'contentPanelMethodList';
                                }
                                else
                                {
                                    // set the content width
                                    initializeStage();

                                    setScrollAreaHeight('contentLeftScrollShipping', 'shippingBack');

                                    document.getElementById('contentLeftScrollShipping').scrollTop = gScrollPositionShipping;

                                    contentPanelShipping.style.marginLeft = 0;

                                    gActivePanel = 'contentPanelShipping';
                                }

                                document.getElementById('contentPanelUpdateAddress').style.display = 'none';

                            break;
                        }
                    }
                }

                /**
                 * activeStore
                 *
                 * Select the store clicked
                 */
                function activeStore()
                {
                    var storeLength = document.getElementsByName('store').length;
                    for (var i = 0; i < storeLength; i++)
                    {
                        var elm = document.getElementsByName('store')[i];
                        if (elm.checked)
                        {
                            elm.parentNode.classList.add('optionSelected');
                        }
                        else
                        {
                            elm.parentNode.classList.remove('optionSelected');
                        }
                    }
                }

                /**
                 * getShippingRateCode
                 *
                 * Return the shipping rate code
                 */
                function getShippingRateCode()
                {
                    var shippingRateCode = "";
                    if (gShippingRateCode.length != 0)
                    {
                        var shippingmethodsLenghth = document.getElementsByName('shippingmethods').length;
                        for (var i = 0; i < shippingmethodsLenghth; i++)
                        {
                            var elm = document.getElementsByName('shippingmethods')[i];
                            if (elm.checked)
                            {
                                shippingRateCode = elm.value;
                            }
                        }
                    }
                    return shippingRateCode;
                }

                function fnShowStoreInfo(pElement)
                {
                    return showStoreInfo(pElement.getAttribute("data-storecode"), pElement.getAttribute("data-externalstore"));
                }

/*** STAGE PAYMENT ***/

				/**
				* Calls the appropriate complete action.
				* @returns {Boolean}
				*/
				function orderButtonCompleteOrder()
				{
					var tsandcsCheckBox = document.getElementById('ordertermsandconditions');
					var paymentsVisible = (document.getElementById('paymenttableobj').style.display != 'none');

					// check if the terms and conditions option exists
					if (tsandcsCheckBox)
					{
						// has the terms and conditions option not checked? if so return false early.
						if (! tsandcsCheckBox.checked)
						{
                            // Mark the terms and conditions confirmation check box as required.
                            document.getElementById('ordertermsandconditionscontainer').classList.add('termsAndConditionsHighLight');

                            // Display Message asking for confirmation of reading terms and conditions.
                            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorAcceptTermsAndConditions');?>
", function(e) {
                                closeDialog(e);
                            });

							return false;
						}
					}

					if ((paymentsVisible) && (gRequestPaymentParamsRemotely))
					{
                        var paymentMethod = '';
                        var paymentMethodsLength = document.getElementsByName('paymentmethods').length;
                        
                        for (var i = 0; i < paymentMethodsLength; i++)
                        {
                            var elm = document.getElementsByName('paymentmethods')[i];
                            
                            if (elm.checked)
                            {
                                paymentMethod = elm.value;
                                break;
                            }
                        }
                        
                        if (paymentMethod == "KLARNA")
                        {
                            callKlarnaEndPoint();
                        }
                        else
                        {
                            callEndPoint();
                        }
					}
					else
					{
						acceptDataEntry();
					}
				}

                /**
                 * acceptTermsAndConditions
                 *
                 * Change the order status button when the terms and conditions are clicked
                 */
                function acceptTermsAndConditions()
                {
					var tsandcsCheckBox = document.getElementById('ordertermsandconditions');

					// check if the terms and conditions option exists
					if (tsandcsCheckBox)
					{
						// has the terms and conditions option been checked?
						if (tsandcsCheckBox.checked)
						{
                            // Remove the required field indicator.
                            document.getElementById('ordertermsandconditionscontainer').classList.remove('termsAndConditionsHighLight');
						}
					}

                }

                /**
                 * paymentMethodClick
                 *
                 * Select the payment method clicked
                 */
                function paymentMethodClick()
                {
                    /* loop through all the shpping methods to see which one has been selected */
                    var paymentMethodsLength = document.getElementsByName('paymentmethods').length;
                    for (var i = 0; i < paymentMethodsLength; i++)
                    {
                        var elm = document.getElementsByName('paymentmethods')[i];
                        if (elm.checked)
                        {
                            elm.parentNode.classList.add('optionSelected');
                        }
                        else
                        {
                            elm.parentNode.classList.remove('optionSelected');
                        }
                    }
                }

                /**
                 * getPaymentMethodCodeRaw
                 *
                 * Return the payment method raw code
                 */
                function getPaymentMethodCodeRaw()
                {
                    var paymentMethodCode = "";
                    if (gPaymentMethodCode != "NONE")
                    {
                        var radioObj = document.getElementsByName('paymentmethods');

                        if (gPaymentMethodCode.length != 0 && radioObj)
                        {
                            var radioLength = radioObj.length;

                            if (radioLength == undefined)
                            {
                                if (radioObj.checked)
                                {
                                    paymentMethodCode = radioObj.value;
                                }
                            }
                            else
                            {
                                for (i = 0; i < radioLength; i++)
                                {
                                    if (radioObj[i].checked)
                                    {
                                        paymentMethodCode = radioObj[i].value;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        paymentMethodCode = gPaymentMethodCode;
                    }
                    return paymentMethodCode;
                }

                /**
                 * getPaymentMethodAction
                 *
                 * Return the payment method action
                 */
                function getPaymentMethodAction()
                {
                    var paymentMethodAction = "";
                    var radioObj = document.getElementsByName('paymentmethods');
                    if (gPaymentMethodCode.length != 0)
                    {
                        var radioLength = radioObj.length;
                        if (radioLength == undefined)
                        {
                            if (radioObj.checked)
                            {
                                paymentMethodAction = radioObj.getAttribute("action");
                            }
                        }
                        else
                        {
                            for (i = 0; i < radioLength; i++)
                            {
                                if (radioObj[i].checked)
                                {
                                    paymentMethodAction = radioObj[i].getAttribute("action");
                                    break;
                                }
                            }
                        }
                    }
                    return paymentMethodAction;
                }

                function showConfirmation()
                {
                    // show the confirmation panel
                    var contentPanelConfirmation = document.getElementById('contentPanelConfirmation');
                    contentPanelConfirmation.style.width = gScreenWidth + 'px';
                    contentPanelConfirmation.style.display= 'block';

                    // initialize the size of the html elments
                    initializeConfimation();

                    // slide the payment panel
                    var contentPanelPayment = document.getElementById('contentPanelPayment');
                    contentPanelPayment.style.marginLeft = '-' + gScreenWidth + 'px';
                }

                function showCancellation()
                {
                    // show the cancellation panel
                    var contentBlocCancel = document.getElementById('contentBlocCancel');
                    contentBlocCancel.style.width = gScreenWidth + 'px';
                    contentBlocCancel.style.display= 'block';

                    // initialize the size of the html elments
                    initializeCancelation();

                    // slide the application panel
                    var contentBlocSite = document.getElementById('contentBlocSite');
                    contentBlocSite.style.width = gScreenWidth + 'px';
                    contentBlocSite.style.marginLeft = '-' + gScreenWidth + 'px';
                }

                function fnShowHideComponents(pElement)
                {
                    return showHideComponents(pElement.getAttribute('data-orderlineid'), pElement.getAttribute('data-isfooter') === 'true');
                }

                function showHideComponents(pElementId, pIsFooter)
                {
                    var objElement = document.getElementById('contentCustomise_' + pElementId);
                    var linkElment = document.getElementById('linkToggle_' + pElementId);

                    if (objElement.style.display == 'none')
                    {
                        objElement.style.display = 'block';
                        linkElment.className = 'showHideTitle';
                        if (pIsFooter)
                        {
                            linkElment.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHideDetails');?>
";
                        }
                        else
                        {
                            linkElment.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHideOptions');?>
";
                        }
                    }
                    else
                    {
                        objElement.style.display = 'none';
                        linkElment.className = 'showHideTitle hidden';
                        if (pIsFooter)
                        {
                            linkElment.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowDetails');?>
";
                        }
                        else
                        {
                            linkElment.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowOptions');?>
";
                        }
                    }
                }

                function fnForceUpperAlphaNumeric(pElement, pEvent)
                {
                    if (pElement.value != '')
                    {
                        // Check for enter key. 
                        if (enterKeyPressed(pEvent))
                        {
                            if (pElement.id == 'vouchercode')
                            {
                                setVoucher();
                            }
                            else
                            {
                                setGiftCard();
                            }
                        }
                        else
                        {
                          	forceUpperAlphaNumeric(pElement);
                        }
                    }

                    return false;
                }

                // Wrapper for window redirection on order confirmation.
                function fnRedirect(pElement)
                {
                    window.location.replace(pElement.getAttribute('data-url'));
                }

                

            //]]>
        <?php echo '</script'; ?>
>

        <?php echo '<script'; ?>
 type="text/javascript" id="mainjavascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        //<![CDATA[

            <?php $_smarty_tpl->_subTemplateRender("file:order/jobticket.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

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
        <div id="innerBox" class="innerBox hide"></div>
        <div id="innerBoxPadding" class="innerBoxPadding hide"></div>

        <div id="containerComponentDescription" class="componentBloc hide"></div>
        <div id="containerHighLight" class="componentHighLight hide"></div>

        <div id="dropDownGeneric" class="wizard-dropdown hide"></div>

        <!-- END HIDDEN DIV TO ACCESS STYLE -->

        <div id="outerPage" class="outerPage">

            <div id="headerSmall" class="header">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>

            <div class="tpxPopupOption" id="language-list-popup">
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

                <div id="contentAjaxQty" class="slideAnimation">

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                        <?php $_smarty_tpl->_subTemplateRender("file:order/jobticketajax_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

                    <?php }?>

                </div>

                <div id="contentAjaxShipping" class="slideAnimation"></div>

                <div id="contentAjaxPayment" class="slideAnimation">

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                        <?php $_smarty_tpl->_subTemplateRender("file:order/jobticketajax_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

                    <?php }?>

                </div>

                <div class="clear"></div>

            </div> <!-- contentBlocSite -->

            <div id="contentBlocCancel" class="contentBlocSite">

            </div> <!-- contentBlocCancel -->

            <div class="clear"></div>

        </div> <!-- outer-page -->

        <div style="display:none">
            <form id="submitform" name="submitform" method="post" accept-charset="utf-8" action="">
                <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" name="itemqty" value="<?php echo $_smarty_tpl->tpl_vars['itemqty']->value;?>
"/>
                <input type="hidden" name="sameshippingandbillingaddress" value=""/>
                <input type="hidden" name="shippingratecode" value=""/>
                <input type="hidden" name="paymentmethodcode" value=""/>
                <input type="hidden" name="paymentgatewaycode" value=""/>
                <input type="hidden" name="requiresdelivery" value=""/>
                <input type="hidden" name="vouchercode" value=""/>
                <input type="hidden" name="previousstage" value="<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
"/>
                <input type="hidden" name="stage" value="<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
"/>
                <input type="hidden" name="section" value=""/>
                <input type="hidden" name="orderlineid" value=""/>
                <input type="hidden" name="giftcardcode" value=""/>
                <input type="hidden" name="showgiftcardmessage" value="0"/>
                <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
                <?php echo $_smarty_tpl->tpl_vars['metadataform']->value;?>

            </form>
        </div>
    </body>
</html><?php }
}
