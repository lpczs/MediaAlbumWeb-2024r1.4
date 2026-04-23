<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:35:26
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\ubbpro\email\customer_orderconfirmation3\email.txt' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb5e3b3ff2_32169796',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cf59f901bde6265b4ee2b194f727c403bdfe691f' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\ubbpro\\email\\customer_orderconfirmation3\\email.txt',
      1 => 1643161788,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb5e3b3ff2_32169796 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - [NEW ORDER] Order Confirmation No: '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
' 
Dear <?php echo $_smarty_tpl->tpl_vars['billingcontactfirstname']->value;?>
,

Thank you for your purchase with Ubabybaby! Once the payment has been confirmed, we will begin working on your UBB product. We estimate that the product will be finished around 7 to 10 working days. If the order includes a book box or gift box, the production time will be extended around 5 working days.  

Please kindly read the order information below. If any  of the information below is incorrect, please do not hesitate to contact us at 3655-6123 to amend. 
<?php echo $_smarty_tpl->tpl_vars['emailContent']->value;?>
 

Tel:<?php echo $_smarty_tpl->tpl_vars['billingcustomertelephonenumber']->value;?>

Email Address:<?php echo $_smarty_tpl->tpl_vars['billingcustomeremailaddress']->value;?>



You can access your account at any time via the URL
<?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>


Thank you again for your purchase with Ubabybaby.
© 2022 Ubabybaby, Inc. All rights reserved. 

<?php }
}
