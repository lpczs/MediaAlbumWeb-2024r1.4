<?php
/* Smarty version 4.5.3, created on 2026-03-09 07:38:48
  from 'C:\TAOPIX\MediaAlbumWeb\Customise\email\customer_orderconfirmation_reorder\email.txt' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ae79086144e0_94793048',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '172b810c6750c33246561dd97484315d5d71071a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Customise\\email\\customer_orderconfirmation_reorder\\email.txt',
      1 => 1726154402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ae79086144e0_94793048 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - Reorder! Order '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
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
