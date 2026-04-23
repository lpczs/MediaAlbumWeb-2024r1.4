<?php
/* Smarty version 4.5.3, created on 2026-03-06 04:45:35
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\sidebarcontactdetails_default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa5bef85fcb7_81356663',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '782991b06ac2f2e5054dffbbfba9eced13f172ee' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\sidebarcontactdetails_default.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa5bef85fcb7_81356663 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['supporttelephonenumber']->value != '' || $_smarty_tpl->tpl_vars['supportemailaddress']->value != '') {?>
    <div class="side-panel section">
        <h2 class="title-bar title-bar-panel">
            <div class="textIcon"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerSupport');?>
</div>
            <img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/contact_icon.png" alt="" />
            <div class="clear"></div>
        </h2>
        <?php if ($_smarty_tpl->tpl_vars['supporttelephonenumber']->value != '') {?>
            <div class="contentDotted">
                <div class="titleDetailPanel">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerSupportLine');?>
:
                </div>
                <div class="sidebaraccount_text">
                    <?php echo $_smarty_tpl->tpl_vars['supporttelephonenumber']->value;?>

                </div>
                <div class="contentDottedImage"></div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['supportemailaddress']->value != '') {?>
            <div class="content">
                <div class="titleDetailPanel">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerSupportEmail');?>

                </div>
                <div class="sidebaraccount_text">
                    <a href="mailto:<?php echo $_smarty_tpl->tpl_vars['supportemailaddress']->value;?>
">
                        <?php echo $_smarty_tpl->tpl_vars['supportemailaddress']->value;?>

                    </a>
                </div>
            </div>
        <?php }?>
    </div>
<?php }
}
}
