<?php
/* Smarty version 4.5.3, created on 2026-03-06 14:19:07
  from 'C:\TAOPIX\MediaAlbumWeb\templates\header_share_online.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aae25b87ea82_24740189',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fff6a972049ad109c8cb4aac2c5c47c76f849ba5' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\header_share_online.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aae25b87ea82_24740189 (Smarty_Internal_Template $_smarty_tpl) {
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
