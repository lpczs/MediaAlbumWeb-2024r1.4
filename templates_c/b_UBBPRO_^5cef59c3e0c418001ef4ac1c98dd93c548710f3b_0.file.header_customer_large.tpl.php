<?php
/* Smarty version 4.5.3, created on 2026-03-12 10:51:01
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\header_customer_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b29a95336571_91749293',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5cef59c3e0c418001ef4ac1c98dd93c548710f3b' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\header_customer_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b29a95336571_91749293 (Smarty_Internal_Template $_smarty_tpl) {
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
    <?php echo $_smarty_tpl->tpl_vars['systemlanguagelist']->value;?>

</div>
<div class="clear"></div><?php }
}
