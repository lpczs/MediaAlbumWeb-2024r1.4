<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:48
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\sidebarcontactdetails_default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb38706df2_20570716',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c59736fba3920a4e9c8fb88d0ed51484f079c87a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\sidebarcontactdetails_default.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb38706df2_20570716 (Smarty_Internal_Template $_smarty_tpl) {
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
