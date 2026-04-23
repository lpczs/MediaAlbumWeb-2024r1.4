<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:26
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\updateaddress2_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd93a6d3843_66894296',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b4e48a19402e6cc362295ef52d7461fcf98512dc' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\updateaddress2_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_small.tpl' => 1,
    'file:order/updateaddress.tpl' => 1,
    'file:header_small.tpl' => 1,
    'file:addressform.tpl' => 1,
  ),
),false)) {
function content_69abd93a6d3843_66894296 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
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
                    document.submitformaddress.fsaction.value = "<?php echo $_smarty_tpl->tpl_vars['refreshaction']->value;?>
";
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
            

            //]]>
        <?php echo '</script'; ?>
>

        <?php echo '<script'; ?>
 type="text/javascript" id="mainjavascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        //<![CDATA[

            <?php $_smarty_tpl->_subTemplateRender("file:order/updateaddress.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
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

        <div id="containerHighLight" class="componentHighLight hide"></div>

        <div id="dropDownGeneric" class="wizard-dropdown hide"></div>

        <!-- END HIDDEN DIV TO ACCESS STYLE -->

        <div id="outerPage" class="outerPage">

            <div id="headerSmall" class="header">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender("file:header_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
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

            <div id="contentBlocSite" class="contentBlocSite">

                <div id="loadingGif" class="loadingGif"></div>

                <div id="contentRightScrollAddress" class="contentScrollCart">

                    <div class="contentVisible">

                         <div class="pageLabel">
                             <?php echo $_smarty_tpl->tpl_vars['title']->value;?>

                         </div>
                         <div class="outerBox outerBoxPadding outerBoxMarginBottom">
                             <div id="contentAddressForm">
                                 <?php $_smarty_tpl->_subTemplateRender("file:addressform.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                             </div>
                         </div>
                         <div class="btnUpdateLarge" data-decorator="verifyAddress">
                             <div id="updateButtonLarge" class="btnUpdateContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
                         </div>

                     </div> <!-- contentVisible -->

                </div> <!-- contentScrollCart -->

            </div> <!-- contentBlocSite -->

        </div> <!-- outer-page -->

        <div style="display:none">
             <form id="submitformaddress" name="submitformaddress" method="post" accept-charset="utf-8" action="#">
                <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
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
                <input type="hidden" name="previousstage" value="<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
"/>
                <input type="hidden" name="stage" value="<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
"/>
                 <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
            </form>
        </div>
    </body>
</html><?php }
}
