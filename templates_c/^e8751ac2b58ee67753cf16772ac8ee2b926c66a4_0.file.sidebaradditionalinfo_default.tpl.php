<?php
/* Smarty version 4.5.3, created on 2026-03-06 04:45:51
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\sidebaradditionalinfo_default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa5bff6ba0e0_14770943',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e8751ac2b58ee67753cf16772ac8ee2b926c66a4' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\sidebaradditionalinfo_default.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa5bff6ba0e0_14770943 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['footer']->value != '') {?>
<div id="footer" class="section">
	<h2 class="title-bar"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalInformation');?>
</h2>
	<div class="content-footer">
		<?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['footer']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
	</div>
</div>
<?php }
}
}
