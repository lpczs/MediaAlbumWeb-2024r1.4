<?php
/* Smarty version 4.5.3, created on 2026-04-22 07:50:17
  from 'C:\TAOPIX\MediaAlbumWeb\templates\header_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e87db91e6198_48005125',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b4292e88450fc77c14a7ad37ab955f5eaf0eba78' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\header_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e87db91e6198_48005125 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="headerLeft">
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

	<?php if (preg_match_all('/[^\s]/u',$_smarty_tpl->tpl_vars['systemlanguagelist']->value, $tmp) > 0) {?>

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="languageSection" id="languageSelector" data-decorator="toggleLanguageOption">
        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/language_icon_v2.png" alt="" class="imgLanguage" />
        <div class="languageImgPopup" id="img-language-popup"></div>
    </div> <!-- languageSection -->

	<?php }?>


    <div class="clear"></div>

</div> <!-- headerRight -->

<div class="clear"></div><?php }
}
