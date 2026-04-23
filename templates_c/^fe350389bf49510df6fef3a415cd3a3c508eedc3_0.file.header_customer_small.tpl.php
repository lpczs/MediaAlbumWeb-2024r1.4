<?php
/* Smarty version 4.5.3, created on 2026-03-09 03:45:04
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\header_customer_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ae4240da2614_73319863',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fe350389bf49510df6fef3a415cd3a3c508eedc3' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\header_customer_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ae4240da2614_73319863 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<div class="headerLeftCustomer">
	<?php if ($_smarty_tpl->tpl_vars['mainwebsiteurl']->value == '') {?>
        <img src="<?php echo $_smarty_tpl->tpl_vars['headerlogoasset']->value;?>
" alt=""/>
    <?php } else { ?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['mainwebsiteurl']->value;?>
" border="0">
            <img src="<?php echo $_smarty_tpl->tpl_vars['headerlogoasset']->value;?>
" alt=""/>
        </a>
    <?php }?>
</div>
<div class="headerRight">

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="languageSection" data-decorator="toggleLanguageOption">
        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/language_icon_v2.png" alt="" class="imgLanguage" />
        <div class="languageImgPopup" id="img-language-popup"></div>
    </div>

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="logoutSection" data-decorator="fnLogout">
        <form id="headerform" method="post" action="#">
            <input type="hidden" id="header_ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" />
            <input type="hidden" id="header_ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
            <input type="hidden" id="header_fsaction" name="fsaction" value="<?php echo $_smarty_tpl->tpl_vars['logoutfsaction']->value;?>
" />
            <input type="hidden" id="header_basketref" name="basketref" value="<?php echo $_smarty_tpl->tpl_vars['basketref']->value;?>
" />
            <input type="hidden" id="header_webbrandcode" name="webbrandcode" value="<?php echo $_smarty_tpl->tpl_vars['webbrandcode']->value;?>
" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        </form>
        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/logout_v2.png" alt="" class="imgAbout" />
    </div>

    <div class="clear"></div>

</div> <!-- headerRight -->

<div class="clear"></div><?php }
}
