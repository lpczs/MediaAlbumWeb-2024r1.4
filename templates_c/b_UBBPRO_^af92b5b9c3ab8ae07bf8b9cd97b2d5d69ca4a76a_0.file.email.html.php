<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:35:26
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\\ubbpro\email\customer_orderconfirmation3\email.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb5e0c8eb3_19079608',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'af92b5b9c3ab8ae07bf8b9cd97b2d5d69ca4a76a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\\\ubbpro\\email\\customer_orderconfirmation3\\email.html',
      1 => 1711071410,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb5e0c8eb3_19079608 (Smarty_Internal_Template $_smarty_tpl) {
?><table style="border: 1px solid #000000; border-collapse : collapse; margin-left: 1%; width:600px;">
    <!-- TAOPIX logo-->
    <tr>
        <td height="80px">
            <img src="<?php echo $_smarty_tpl->tpl_vars['brandLogo']->value;?>
" id="headerImage"  height="34px"/>
        </td>
    </tr>
    <tr style="background-color: #575757; text-indent: 12px; font-size: 13px; font-weight:bold; color: #FFFFFF; font-family: Lucida Grande;" >
        <td width="100%" style="padding-top: 5px; padding-bottom: 5px; word-break:break-all;">
            [NEW ORDER] Order Confirmation No: '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
' 
        </td>
    </tr>
    <tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 12px;  display: block; color: #575757; font-family: Lucida Grande;">
        <td style="word-break:break-all;">
           Dear <?php echo $_smarty_tpl->tpl_vars['billingcontactfirstname']->value;?>
,
            <br />
            <br />
            Thank you for your purchase with Ubabybaby! Once the payment has been confirmed, we will begin working on your UBB product. We estimate that the product will be finished around 7 to 10 working days. If the order includes a book box or gift box, the production time will be extended around 5 working days.  
            <br /><br />
            Please kindly read the order information below. If any  of the information below is incorrect, please do not hesitate to contact us at 3655-6123 to amend. 
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
	<tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 10px;  display: block; color: #575757; font-family: Lucida Grande;">
	 <td>
	    <table>
		   <tr><th style="text-align:left;">Customer Info</th></tr>
		   <tr><td>Tel:<?php echo $_smarty_tpl->tpl_vars['billingcustomertelephonenumber']->value;?>
</td></tr>
		   <tr><td>Email Address:<a href="mailto:<?php echo $_smarty_tpl->tpl_vars['billingcustomeremailaddress']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['billingcustomeremailaddress']->value;?>
</a></td></tr>

		</table>
	      
	 </td>
	 
	</tr>
	<tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 12px;  display: block; color: #575757; font-family: Lucida Grande;">
	   <td>Thank you again for your purchase with Ubabybaby.</td>
	</tr>
    <tr style="padding: 10px; border: 0px solid #CDCDCD; font-size: 10px;  display: inline-block; color: #575757; font-family: Lucida Grande;text-align:right;width:620px;">    
        <td style="width: 100%; text-align:right;display:inline-block;">
            © 2022 Ubabybaby, Inc. All rights reserved. 
        </td>
    </tr>
</table><?php }
}
