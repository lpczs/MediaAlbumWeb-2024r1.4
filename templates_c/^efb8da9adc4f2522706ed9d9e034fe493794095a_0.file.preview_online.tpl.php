<?php
/* Smarty version 4.5.3, created on 2026-03-06 14:19:07
  from 'C:\TAOPIX\MediaAlbumWeb\templates\share\preview_online.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aae25b245268_67095844',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'efb8da9adc4f2522706ed9d9e034fe493794095a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\share\\preview_online.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
  ),
),false)) {
function content_69aae25b245268_67095844 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),1=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
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

        <!-- NOTE VIEWPORT SETTINGS - important to prevent screen size calculations, minimal-ui to support iOS8 -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
        <title><?php echo $_smarty_tpl->tpl_vars['projectname']->value;?>
</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;
echo smarty_function_asset(array('file'=>'/css/styles.css'),$_smarty_tpl);?>
" type="text/css">
    </head>
    <body class="share-preview <?php if ($_smarty_tpl->tpl_vars['sharehidebranding']->value != 0) {?>no-branding<?php }?>">
        <div id="header" class="header">
            <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
        </div>

        <?php if (!$_smarty_tpl->tpl_vars['error']->value) {?>
            <iframe id="tframe" class="preview-iframe" scrolling="no" seamless="seamless" src="<?php echo $_smarty_tpl->tpl_vars['designurl']->value;?>
" >
            </iframe>
        <?php } else { ?>
            <div class="share-error-message">
                <p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
            </div>
        <?php }?>

        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
            var shareHideBranding = <?php echo $_smarty_tpl->tpl_vars['sharehidebranding']->value;?>
;
            init();

            function init()
            {
                registerListeners();
            }

            function registerListeners()
            {
                // We only need these listeners if we are on iOS device
                if (navigator.userAgent.match(/iphone|ipad/i))
                {

                    // do not reguster listeners if Chrome Browser or Safari Browser with version >= 13
                    if(!navigator.userAgent.match(/CriOS\/\d*/) && (navigator.userAgent.match(/Safari\/\d*/) && parseInt(navigator.userAgent.match(/Version\/\d*/)[0].slice(8)) >= 13)){
                        return;
                    }

                    window.addEventListener('orientationchange',resizeIframe);
                    window.addEventListener('DOMContentLoaded',resizeIframeOnDOMLoaded);
                }
            }

            function resizeIframeOnDOMLoaded(){
                resizeIframe(false);
            }

            // Need to resizeIframe as iOS devices make height adjustments as part of their page scroll implementation
            function resizeIframe(reload)
            {
                // Workaround for DOM race condition issues when calculating how to present the page
                if(reload) {
                    document.body.innerHTML = '';
                    window.location.reload();
                }
            }
        <?php echo '</script'; ?>
>

    </body>
</html><?php }
}
