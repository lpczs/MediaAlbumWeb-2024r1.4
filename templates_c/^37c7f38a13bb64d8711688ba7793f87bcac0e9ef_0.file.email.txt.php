<?php
/* Smarty version 4.5.3, created on 2026-03-23 02:11:23
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\ubbpro\email\customer_detailsupdated\email.txt' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69c0a14b270876_17935815',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '37c7f38a13bb64d8711688ba7793f87bcac0e9ef' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\ubbpro\\email\\customer_detailsupdated\\email.txt',
      1 => 1643103436,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c0a14b270876_17935815 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - Changed Account Details

Hello <?php echo $_smarty_tpl->tpl_vars['user']->value;?>


You have changed the following details in your account for <?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>
 </p>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['changes']->value, 'i', false, 'k');
$_smarty_tpl->tpl_vars['i']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->do_else = false;
?>
• <?php echo $_smarty_tpl->tpl_vars['i']->value[0];?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

If this wasn't you, please contact customer services.

<?php echo $_smarty_tpl->tpl_vars['emailsignature']->value;?>

<?php }
}
