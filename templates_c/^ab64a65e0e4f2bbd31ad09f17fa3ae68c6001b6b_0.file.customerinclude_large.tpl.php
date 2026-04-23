<?php
/* Smarty version 4.5.3, created on 2026-03-06 04:13:38
  from 'C:\TAOPIX\MediaAlbumWeb\templates\includes\customerinclude_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa54729daee3_78952529',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ab64a65e0e4f2bbd31ad09f17fa3ae68c6001b6b' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\includes\\customerinclude_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/customerinclude.tpl' => 1,
  ),
),false)) {
function content_69aa54729daee3_78952529 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
$_smarty_tpl->_subTemplateRender("file:includes/customerinclude.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;
echo smarty_function_asset(array('file'=>'/css/csscustomer_large.css'),$_smarty_tpl);?>
" media="screen"/><?php }
}
