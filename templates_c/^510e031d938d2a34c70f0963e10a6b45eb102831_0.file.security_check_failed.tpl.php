<?php
/* Smarty version 4.5.3, created on 2026-03-15 20:45:33
  from 'C:\TAOPIX\MediaAlbumWeb\templates\security_check_failed.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b71a6de04200_30408140',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '510e031d938d2a34c70f0963e10a6b45eb102831' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\security_check_failed.tpl',
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
function content_69b71a6de04200_30408140 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelWelcome');?>
</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />

        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    </head>

    <body>
        <div class="outer-page fullsize-outer-page responsive-page">
            <div id="header" class="headertop">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>

            <div class="page-content">
                <h2 class="title-bar">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Error');?>

                </h2>

                <div class="content error">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorSecurityChecksFailed');?>

                </div>
            </div>
        </div>
    </body>
</html>
<?php }
}
