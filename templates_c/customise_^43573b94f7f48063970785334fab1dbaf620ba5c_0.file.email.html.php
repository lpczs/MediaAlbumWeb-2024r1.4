<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:15:28
  from 'C:\TAOPIX\MediaAlbumWeb\Customise\\email\customer_orderconfirmation\email.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa62f0a7b2c8_21902300',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '43573b94f7f48063970785334fab1dbaf620ba5c' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Customise\\\\email\\customer_orderconfirmation\\email.html',
      1 => 1726154402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa62f0a7b2c8_21902300 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - Order '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
' Confirmation</title>
	</head>
	<body>

		<table style="border: 1px solid #000000; border-collapse : collapse; margin-left: 1%; width:600px;">
			<!-- TAOPIX logo-->
			<tr>
				<td height="80px">
					<img src="<?php echo $_smarty_tpl->tpl_vars['brandLogo']->value;?>
" id="headerImage"  height="60px"/>
				</td>
			</tr>
			<tr style="background-color: #575757; text-indent: 12px; font-size: 13px; font-weight:bold; color: #FFFFFF; font-family: Lucida Grande;" >
				<td width="100%" style="padding-top: 5px; padding-bottom: 5px; word-break:break-all;">
					Order '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
' Confirmation
				</td>
			</tr>
			<tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 12px;  display: block; color: #575757; font-family: Lucida Grande;">
				<td style="word-break:break-all;">
					Hello <?php echo $_smarty_tpl->tpl_vars['billingcontactfirstname']->value;?>
,
					<br />
					<br />
					Thank you for your order.
					<br />
					Your order number is <strong><?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
</strong>, please quote this in any correspondence with us.
					<br />
					<?php if ($_smarty_tpl->tpl_vars['additionalpaymentinfo']->value != '') {?>
					<br />
					<?php echo $_smarty_tpl->tpl_vars['additionalpaymentinfo']->value;?>

					<br />
					<?php }?>
					<br />
					The following is a summary of what you have ordered:
					<br />
					<br />

				</td>
			</tr>
			<tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 12px;  display: block; color: #575757; font-family: Lucida Grande;">
				<td style="word-break:break-all;">
					<?php echo $_smarty_tpl->tpl_vars['emailContent']->value;?>

				</td>
			</tr>
			<tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 12px;  display: block; color: #575757; font-family: Lucida Grande;">
				<td style="word-break:break-all;">
					You can access your account at any time via the URL <a href="<?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>
</a>
				</td>
			</tr>
			<tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 12px;  display: block; color: #575757; font-family: Lucida Grande;">
				<td style="word-break:break-all;">
					<?php echo $_smarty_tpl->tpl_vars['emailsignature']->value;?>

				</td>
			</tr>
		</table>
	</body>
</html><?php }
}
