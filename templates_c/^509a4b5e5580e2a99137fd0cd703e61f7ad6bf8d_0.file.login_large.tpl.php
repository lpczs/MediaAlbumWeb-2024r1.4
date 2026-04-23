<?php
/* Smarty version 4.5.3, created on 2026-03-06 04:13:37
  from 'C:\TAOPIX\MediaAlbumWeb\templates\login_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa5471cf98a1_26883495',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '509a4b5e5580e2a99137fd0cd703e61f7ad6bf8d' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\login_large.tpl',
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
function content_69aa5471cf98a1_26883495 (Smarty_Internal_Template $_smarty_tpl) {
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
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelWelcome');?>
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

    

            // laoding image
            var gAlerts = 0;

            function setSystemLanguage()
            {
                changeSystemLanguage("<?php echo $_smarty_tpl->tpl_vars['changelanguageinitfsaction']->value;?>
", "submitform", 'post');
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

    

    <?php if ($_smarty_tpl->tpl_vars['resetpasswordenabled']->value) {?>

        

            function forgotPassword()
            {
                document.getElementById("login").value = document.getElementById('login2Large').value;
                document.getElementById("fsaction").value = "Welcome.initForgotPassword";
                document.getElementById("submitform").submit();
                return false;
            }

        

    <?php }?> 
    

            /* only if password reset is enabled */
            function createAccount()
            {
                document.getElementById("fsaction").value = "Welcome.initNewAccount";
                document.getElementById("submitform").submit();
            }

            function validateLoginLargeScreen(e)
            {
                // Prevent the Sign in input button from triggering the input of the wrong form.
                e.preventDefault();

                var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
";
                gAlerts = 0;
                if (document.getElementById('login2Large').value.length == 0)
                {
                    message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoUserName');?>
";
                    highlight("login2Large");
                }

                if (document.getElementById('password2Large').value.length == 0)
                {
                    message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPassword');?>
";
                    highlight("password2Large");
                }

                if (gAlerts > 0)
                {
                    alert(message);
                    return false;
                }
                else
                {
                    var format = ((document.location.protocol != 'https:') ? 1 : 0);

                    /* copy the values into the form we will submit and then submit it to the server */
                    document.getElementById("login").value = document.getElementById('login2Large').value;
                    document.getElementById("password").value = ((format == 0) ? document.getElementById('password2Large').value : hex_md5(document.getElementById('password2Large').value));
                    document.getElementById("fsaction").value = '<?php echo $_smarty_tpl->tpl_vars['loginfsaction']->value;?>
';
                    document.getElementById("format").value = format;
                    document.getElementById("submitform").submit();

                    return false;
                }
            }

            window.onload = function()
            {
                initializeLargeScreenVersion();

                /* set a cookie to store the local time */
                var theDate = new Date();
                createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

                 /* Create the device type string and then set the cookie */
                var devString = makeDevCookie();
                createCookie("mawdd", devString, 90000);

                // Add listener to langauge select.
                var langSelectElement = document.getElementById('systemlanguagelist');
                if (langSelectElement)
                {
                    langSelectElement.addEventListener('change', function() {
                        return setSystemLanguage();
                    });
                }

    <?php if ($_smarty_tpl->tpl_vars['resetpasswordenabled']->value) {?>
                // Add listener to the forgot password link. 
                var forgotPasswordElement = document.getElementById('forgotPasswordLink');
                if (forgotPasswordElement)
                {
                    forgotPasswordElement.addEventListener('click', function() {
                        return forgotPassword();
                    });
                }
    <?php }?>

                // Add listener to the sign in button
                var loginButtonElement = document.getElementById('loginButtonContainer');
                if (loginButtonElement)
                {
                    loginButtonElement.addEventListener('click', function(pEvent) {
                        validateLoginLargeScreen(pEvent); 
                        return false;
                    });
                }

                // Add listener to create account button.
                var createAccButton = document.getElementById('backButton');
                if (createAccButton)
                {
                    createAccButton.addEventListener('click', function() {
                        createAccount();
                    });
                }

				var togglePassword2LargeElement = document.getElementById('togglepassword2large');
                if (togglePassword2LargeElement)
                {
					togglePassword2LargeElement.addEventListener('click', function() {
                        togglePasswordVisibility(togglePassword2LargeElement, 'password2Large');
                    });
                }
            }

            function initializeLargeScreenVersion()
            {
                if ("<?php echo $_smarty_tpl->tpl_vars['error']->value;?>
".length > 0 || "<?php echo $_smarty_tpl->tpl_vars['info']->value;?>
".length)
                {
                    document.getElementById('message').style.display = 'block';
                }

            

            <?php if ($_smarty_tpl->tpl_vars['login2Template']->value != '') {?>

                

                // exception for IE7
                var browserValid = detectionIEBrowser(8);
                var paddindTop = 0;
                if (browserValid == false)
                {
                    paddindTop = parseInt(getStyle('contentTextNewAccount', 'paddingTop'));
                }
                var objDivLogin = document.getElementById('contentFormLogin');
                var objDivAccount = document.getElementById('contentTextNewAccount');
                var heightLogin = objDivLogin.offsetHeight;
                var heightAccount = objDivAccount.offsetHeight;
                if( heightAccount > heightLogin)
                {
                    objDivLogin.style.height = heightAccount + 'px';
                    objDivAccount.style.height = (heightAccount - paddindTop) + 'px';
                }
                else
                {
                    objDivLogin.style.height = heightLogin + 'px';
                    objDivAccount.style.height = (heightLogin - paddindTop) + 'px';
                }

                

            <?php }?>

            

            }

            
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
                <div id="pageFooterHolder" class="fullsizepage">
                    <div id="page" class="section">
                        <h2 class="title-bar">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleSignIn');?>

                        </h2>
                        <div class="content log-in-wrap">
                            <noscript>
                                <div class="messageNoScriptLarge">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoJavaScript');?>

                                </div>
                            </noscript>
                            <div id="returningCustomersHolder" <?php if ($_smarty_tpl->tpl_vars['login2Template']->value == '') {?>class="align-center"<?php }?>>
                                <div class="contentLogin">
                                    <form method="post" action="#">
                                        <div id="contentFormLogin">
                                            <div class="<?php echo $_smarty_tpl->tpl_vars['messageareaclass']->value;?>
" id="message">
                                                <p><?php echo $_smarty_tpl->tpl_vars['error']->value;
echo $_smarty_tpl->tpl_vars['info']->value;?>
</p>
                                            </div>
                                            <div class="top_gap_login">
                                                <label for="login2Large"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailorUsername');?>
:</label>
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                                <input type="text" id="login2Large" name="login2Large" value="<?php echo $_smarty_tpl->tpl_vars['loginVal']->value;?>
" class="middle" />
                                                <img class="error_form_image" id="login2LargeCompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                                <div class="clear"></div>
                                            </div>
                                            <div class="top_gap_login">
                                                <label for="password2Large"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPassword');?>
:</label>
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                                <div class="password-input-wrap">
                                                    <div class="password-background">
                                                        <input type="password" id="password2Large" name="password2Large" value="" class="middle" />
                                                        <button type="button" id="togglepassword2large" class="password-visibility password-show"></button>
                                                    </div>
                                                </div>
                                                <img class="error_form_image" id="password2LargeCompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                                <div class="clear"></div>
                                            </div>
                                            <div class="">
                                                <?php if ($_smarty_tpl->tpl_vars['resetpasswordenabled']->value) {?>
                                                    <div class="forgotPasswordLink">
                                                        <a href="#" id="forgotPasswordLink">
                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelForgotPassword');?>

                                                        </a>
                                                    </div>
                                                <?php }?>
                                                <div class="note">
                                                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompulsoryFields');?>

                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btnRight btn-sign-in">
                                            <div class="contentBtn" id="loginButtonContainer">
                                                <div class="btn-green-left" ></div>
                                                <input type="submit" value="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSignIn');?>
" class="btn-submit-green-middle" name="loginButton" id="loginButton"/>
                                                <div class="btn-accept-right"></div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </form>
                                </div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['login2Template']->value != '') {?>
                                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['login2Template']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                            <?php }?>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value != '') {?>
                        <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                    <?php }?>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <form id="submitform" name="submitform" method="post" action="" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />

            <?php if ($_smarty_tpl->tpl_vars['ishighlevel']->value == 1) {?>
            	<input type="hidden" id="groupcode" name="groupcode" value="<?php echo $_smarty_tpl->tpl_vars['groupcode']->value;?>
" />
            <?php }?>

            <input type="hidden" id="fsaction" name="fsaction" value="" autocomplete="off"/>
            <input type="hidden" id="login" name="login" autocomplete="off"/>
            <input type="hidden" id="password" name="password" autocomplete="off"/>
            <input type="hidden" id="format" name="format" autocomplete="off"/>
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
            <input type="hidden" id="registerfsaction" name="registerfsaction" value="<?php echo $_smarty_tpl->tpl_vars['registerfsaction']->value;?>
" />
            <input type="hidden" id="fromregisterlink" name="fromregisterlink" value="<?php echo $_smarty_tpl->tpl_vars['fromregisterlink']->value;?>
" />
            <input type="hidden" id="mobile" name="mobile" value="false" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        </form>
    </body>
</html><?php }
}
