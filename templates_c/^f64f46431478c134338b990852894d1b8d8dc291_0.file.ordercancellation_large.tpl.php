<?php
/* Smarty version 4.5.3, created on 2026-03-23 02:10:02
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\ordercancellation_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69c0a0fab56488_31321837',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f64f46431478c134338b990852894d1b8d8dc291' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\ordercancellation_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
    'file:order/finalstageincludedecorator.tpl' => 1,
    'file:header_large.tpl' => 1,
  ),
),false)) {
function content_69c0a0fab56488_31321837 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderCancellation');?>
</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php $_smarty_tpl->_subTemplateRender("file:order/finalstageincludedecorator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    </head>
    <body>
        <div class="outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
            <div id="header" class="headertop">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender("file:header_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?>
                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarleft']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
            <?php }?>
            <div id="contentHolder">
                <div id="pageFooterHolder" class="<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?>fullsizepage<?php }?>">
                    <div id="page" class="section">
                        <div class="title-bar">
                            <h2 class="title-cancellation">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderCancellation');?>

                            </h2>
                        </div>
                        <div class="content">
                            <div class="cancellationText">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderCancellation1');?>

                            </div>
                            <?php if (($_smarty_tpl->tpl_vars['source']->value == $_smarty_tpl->tpl_vars['TPX_SOURCE_DESKTOP']->value) && ($_smarty_tpl->tpl_vars['reorder']->value != 1)) {?>
                                <div class="cancellationText">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderCancellation2');?>

                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['mainwebsiteurl']->value != '') {?>
                                <div class="contentDottedImage"></div>
                                <div class="btnRightContinue">
                                    <div class="contentBtn" data-decorator="fnRedirect" data-url="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['mainwebsiteurl']->value, ENT_QUOTES, 'UTF-8', true);?>
">
                                        <div class="btn-green-left" ></div>
                                        <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                                        <div class="btn-accept-right"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html><?php }
}
