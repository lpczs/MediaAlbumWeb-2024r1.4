<?php
/* Smarty version 4.5.3, created on 2026-04-22 07:50:16
  from 'C:\TAOPIX\MediaAlbumWeb\templates\includes\customerinclude_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e87db8e835a1_34507848',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7f19744cbf9a5b1a11c729196d7e1d84295b448b' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\includes\\customerinclude_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/customerinclude.tpl' => 1,
  ),
),false)) {
function content_69e87db8e835a1_34507848 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
$_smarty_tpl->_subTemplateRender("file:includes/customerinclude.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;
echo smarty_function_asset(array('file'=>'/css/csscustomer_small.css'),$_smarty_tpl);?>
" media="screen"/>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/functions_small.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
><?php }
}
