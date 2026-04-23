<?php
/* Smarty version 4.5.3, created on 2026-04-23 03:01:10
  from 'C:\TAOPIX\MediaAlbumWeb\templates\forgotpassword_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e98b76418b43_74610765',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d2da1406928ce21fd529f52ba3c9848f668b97c' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\forgotpassword_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
  ),
),false)) {
function content_69e98b76418b43_74610765 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="csrf-token" content="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelForgotPassword');?>
</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
            //<![CDATA[
            
            function setSystemLanguage()
            {
                changeSystemLanguage("Welcome.initForgotPassword", "submitform", 'post');
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

            function validateDataEntry(e)
            {
                // Prevent the Sign in input button from triggering the input of the wrong form.
                e.preventDefault();

                gAlerts = 0;
                var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
                var theForm = document.getElementById('formForgot');
                if (theForm.login2.value.length == 0)
                {
                    message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoUserName');?>
";
                    highlight("login2");
                    gAlerts = 1;
                }

                if (gAlerts > 0)
                {
                    alert(message);
                }
                else
                {
                    /* copy the values into the form we will submit and then submit it to the server */
                    document.getElementById("login").value = theForm.login2.value;
                    document.getElementById("fsaction").value = "Welcome.resetPasswordRequest";
                    document.getElementById("format").value = ((document.location.protocol != 'https:') ? 1 : 0);
                    document.getElementById("submitform").submit();
                }
            }

            function cancelDataEntry()
            {
                document.getElementById("login").value = document.getElementById('login2').value;
				document.getElementById("fsaction").value = "<?php echo $_smarty_tpl->tpl_vars['cancelfsaction']->value;?>
";
                document.getElementById("submitform").submit();
                return false;
            }

            window.onload = function()
            {
                if (("<?php echo $_smarty_tpl->tpl_vars['error']->value;?>
".length > 0) || ("<?php echo $_smarty_tpl->tpl_vars['info']->value;?>
".length > 0))
                {
                    document.getElementById('message').style.display = 'block';
                }

                // Add listener to the sign in button.
                document.getElementById('resetPasswordButton').addEventListener('click', function(pEvent) {
                    validateDataEntry(pEvent); 
                    return false;
                });

                // Add listener to the back button.
                var backButtonElement = document.getElementById('backButton');
                if (backButtonElement)
                {
                    backButtonElement.addEventListener('click', function() {
                        cancelDataEntry();
                        return false;
                    });
                }

                // Add listener to langauge select.
                document.getElementById('systemlanguagelist').addEventListener('change', function() {
                    return setSystemLanguage();
                });
            };
            
                //]]>
        <?php echo '</script'; ?>
>
    </head>
    <body>
        <div class="outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
            <div id="header" class="headertop">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?>
                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarleft']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
            <?php }?>
            <div id="contentHolder">
                <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?>class="fullsizepage"<?php }?>>
                    <div id="page" class="section">
                        <h1 class="title-bar">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelForgotPassword');?>

                        </h1>
                        <noscript>
                            <div class="messageNoScript">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoJavaScript');?>

                            </div>
                        </noscript>
                        <div class="content log-in-wrap reset-password">
                            <div id="resetPasswordHolder" <?php if ($_smarty_tpl->tpl_vars['login2Template']->value == '') {?>class="align-center"<?php }?>>
                                <p>
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TextResetPassword');?>

                                </p>
                                <form id="formForgot" method="post" action="#">
                                    <div class="contentForgot">

                                        <div class="<?php echo $_smarty_tpl->tpl_vars['messageareaclass']->value;?>
" id="message">
                                            <span><?php echo $_smarty_tpl->tpl_vars['error']->value;
echo $_smarty_tpl->tpl_vars['info']->value;?>
</span>
                                        </div>

                                        <div class="top_gap">
                                            <label for="login2"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailorUsername');?>
:</label>
                                            <img class="valign-center" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <input type="text" id="login2" name="login2" value='<?php echo $_smarty_tpl->tpl_vars['loginval']->value;?>
' class="middle" />
                                            <img id="login2compulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                            <div class="clear"></div>
                                        </div>

                                        <div class="note">
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompulsoryFields');?>

                                        </div>

                                    </div>

                                    <div class="buttonBottomInside buttonForgot">
                                        <?php if ($_smarty_tpl->tpl_vars['showbackbutton']->value) {?>
                                        <div class="btnLeft">
                                            <div class="contentBtn" id="backButton">
                                                <div class="btn-blue-arrow-left" ></div>
                                                <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
                                                <div class="btn-blue-right"></div>
                                            </div>
                                        </div>
                                        <?php }?>
                                        <div class="btnRight">
                                            <div class="contentBtn" id="resetPasswordButton">
                                                <div class="btn-green-left" ></div>
                                                <input type="submit" value="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSendResetLink');?>
" class="btn-submit-green-middle" name="resetButton" id="resetButton" />
                                                <div class="btn-accept-right"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value != '') {?>
                        <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                        <div class="side-outer-panel">
                            <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarcontactdetails']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
            <input type="hidden" id="login" name="login" />
			<input type="hidden" id="prtz" name="prtz" value="<?php echo $_smarty_tpl->tpl_vars['prtz']->value;?>
"/>
            <input type="hidden" id="mawebhluid" name="mawebhluid" value="<?php echo $_smarty_tpl->tpl_vars['mawebhluid']->value;?>
"/>
            <input type="hidden" id="mawebhlbr" name="mawebhlbr" value="<?php echo $_smarty_tpl->tpl_vars['mawebhlbr']->value;?>
"/>
            <input type="hidden" id="fhlbu" name="fhlbu" value="<?php echo $_smarty_tpl->tpl_vars['fhlbu']->value;?>
"/>
            <input type="hidden" id="ishighlevel" name="ishighlevel" value="<?php echo $_smarty_tpl->tpl_vars['ishighlevel']->value;?>
" />
            <input type="hidden" id="format" name="format" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />

			<?php if ($_smarty_tpl->tpl_vars['ishighlevel']->value == 1) {?>
            	<input type="hidden" id="groupcode" name="groupcode" value="<?php echo $_smarty_tpl->tpl_vars['groupcode']->value;?>
" />
				<input type="hidden" id="fromregisterlink" name="fromregisterlink" value="<?php echo $_smarty_tpl->tpl_vars['fromregisterlink']->value;?>
" />
            <?php }?>
            <input type="hidden" id="passwordlinkexpired" name="passwordlinkexpired" value="<?php echo $_smarty_tpl->tpl_vars['passwordlinkexpired']->value;?>
" />
            <input type="hidden" id="passwordresetrequesttoken" name="passwordresetrequesttoken" value="<?php echo $_smarty_tpl->tpl_vars['passwordresetrequesttoken']->value;?>
" />
			<input type="hidden" id="passwordresetdatabasetoken" name="passwordresetdatabasetoken" value="<?php echo $_smarty_tpl->tpl_vars['passwordresetdatabasetoken']->value;?>
" />
        </form>
    </body>
</html><?php }
}
