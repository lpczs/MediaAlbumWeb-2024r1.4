<?php
/* Smarty version 4.5.3, created on 2026-03-06 03:52:50
  from 'C:\TAOPIX\MediaAlbumWeb\templates\header_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa4f92e90728_31819409',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '996b92758f91813981b306507bd5307a3c0fe861' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\header_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa4f92e90728_31819409 (Smarty_Internal_Template $_smarty_tpl) {
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
