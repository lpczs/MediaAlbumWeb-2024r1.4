<?php
/* Smarty version 4.5.3, created on 2026-04-21 07:44:57
  from 'C:\TAOPIX\MediaAlbumWeb\Customise\email\customer_newaccount\email.txt' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e72af9d275f0_05159356',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '94473008051fe2f4bd7dcfcdbf11290de2c6c82f' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Customise\\email\\customer_newaccount\\email.txt',
      1 => 1726154402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e72af9d275f0_05159356 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - New Account Confirmation
Hello <?php echo $_smarty_tpl->tpl_vars['contactfirstname']->value;?>
,

Thank you for registering with <?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 'Online'.

Your login is: <?php echo $_smarty_tpl->tpl_vars['loginname']->value;?>


You can access your account at any time via the URL 
<?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>



<?php echo $_smarty_tpl->tpl_vars['emailsignature']->value;
}
}
