<?php
/* Smarty version 4.5.3, created on 2026-04-21 07:44:57
  from 'C:\TAOPIX\MediaAlbumWeb\Customise\\email\customer_newaccount\email.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e72af9a7d389_51659471',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6a570238349b6181d830270eb0abb98015e001df' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Customise\\\\email\\customer_newaccount\\email.html',
      1 => 1726154402,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e72af9a7d389_51659471 (Smarty_Internal_Template $_smarty_tpl) {
?><html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - New Account Confirmation</title>
        <style type="text/css">
            
            /* Client-specific Styles */
            #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
            body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
            body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */

            /* Reset Styles */
            body{margin:0; padding:0;}
            img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
            table td{border-collapse:collapse;}
            #backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

            /* Template Styles */
            body, #backgroundTable { background-color: #F8F8F8; }

            td.headerContent{
                line-height:100%;
                padding-top: 15px;
                padding-bottom: 30px;
                vertical-align:middle;
            }
            #headerImage{ height:auto; max-width: 800px !important; }

            h1, .h1{
                color: #FFFFFF;
                display: block;
                font-family: Arial, Helvetica, Verdana, sans-serif;
                font-size: 13px;
                font-weight:bold;
                line-height:25px;
                margin-top:0 !important;
                margin-right:0 !important;
                margin-bottom:0px !important;
                margin-left:0 !important;
                text-align: left;
                height: 25px;
                padding-left: 12px;
            }

            #emailTitleBar {
                background-color: #575757;
            }

            #templateBody {
                background-color: #ffffff;
                border: 0;
            }
            #templateBody div{
                color:#333333;
                font-family: Arial, Helvetica, Verdana, sans-serif;
                font-size:12px;
                line-height:16px;
                text-align: left;
            }

            #templateContents {
                border-left-width:1px;
                border-right-width:1px;
                border-bottom-width:1px;
                border-left-style:solid;
                border-right-style:solid;
                border-bottom-style:solid;
                border-left-color:#CDCDCD;
                border-right-color:#CDCDCD;
                border-bottom-color:#CDCDCD;
                border-top: 0;
            }
            
        </style>
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <center>

        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
            <tr>
                <td align="center" valign="top">

                    <!-- // Begin Template Container \\ -->
                    <table border="0" cellpadding="0" cellspacing="0" width="800" id="templateContainer">
                        <tr>
                            <td align="center" valign="top">
                                <!-- // Begin Template Header \\ -->
                                <table border="0" cellpadding="0" cellspacing="0" width="800" id="templateHeader">
                                    <tr>
                                        <td class="headerContent" style="line-height:100%; padding-top: 15px; padding-bottom: 30px; vertical-align:middle;">
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandLogo']->value;?>
" style="max-width:800px;" id="headerImage" />
                                        </td>
                                    </tr>
                                </table>
                                <!-- // End Template Header \\ -->
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">
                                <!-- // Begin Template Body \\ -->
                                <table border="0" cellpadding="0" cellspacing="0" width="800" id="templateBody" bgcolor="ffffff" background="">
                                    <tr>
                                        <td valign="top">
                                            <table id="emailTitleBar" border="0" bgcolor="575757" cellpadding="0" cellspacing="0" height="25px" width="100%">
                                                <tr>
                                                    <td>
                                                        <h1 class="h1" style='padding-left: 12px; margin: 0; height: 25px; line-height:25px; font-size: 13px; font-weight:bold; color: #FFFFFF; display: block; font-family:"Lucida Grande",Helvetica,Arial,Verdana,sans-serif;'><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - New Account Confirmation</h1>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <table border="0" cellpadding="10" cellspacing="0" height="100%" width="100%" id="templateContents" style="border-left-width:1px; border-right-width:1px; border-bottom-width:1px; border-left-style:solid; border-right-style:solid; border-bottom-style:solid; border-left-color:#CDCDCD; border-right-color:#CDCDCD; border-bottom-color:#CDCDCD; border-top: 0;">
                                                <tr>
                                                    <td valign="top">
                                                        <div>
                                                            Hello <?php echo $_smarty_tpl->tpl_vars['contactfirstname']->value;?>

                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td valign="top"><div>
                                                        <p>Thank you for registering with <?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 <i>'Online'</i>. <br>
                                                        <p>Your login is: <?php echo $_smarty_tpl->tpl_vars['loginname']->value;?>
 </p>
                                                        You can access your account at any time via the URL <a href="<?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['homeurl']->value;?>
</a>
                                                    </div></td>
                                                </tr>
                                                <tr>
                                                    <td valign="top">
                                                        <div>
                                                            <?php echo $_smarty_tpl->tpl_vars['emailsignature']->value;?>

                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!-- // End Template Body \\ -->
                            </td>
                        </tr>
                    </table>
                    <!-- // End Template Container \\ -->
                </td>
            </tr>
        </table>

    </center>
</body>
</html><?php }
}
