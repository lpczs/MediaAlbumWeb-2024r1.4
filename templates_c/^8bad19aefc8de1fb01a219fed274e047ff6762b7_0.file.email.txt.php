<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:15:28
  from 'C:\TAOPIX\MediaAlbumWeb\Customise\email\customer_orderconfirmation\email.txt' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa62f0dda188_64675445',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8bad19aefc8de1fb01a219fed274e047ff6762b7' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Customise\\email\\customer_orderconfirmation\\email.txt',
      1 => 1726154402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa62f0dda188_64675445 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - Order '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
' Confirmation
Hello <?php echo $_smarty_tpl->tpl_vars['billingcontactfirstname']->value;?>
,

Thank you for your order. Your order number is <?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
, please quote this in any correspondence with us.

The following is a summary of what you have ordered:
<?php echo $_smarty_tpl->tpl_vars['emailContent']->value;?>


You can access your account at any time via the URL
<?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>


<?php echo $_smarty_tpl->tpl_vars['emailsignature']->value;
}
}
