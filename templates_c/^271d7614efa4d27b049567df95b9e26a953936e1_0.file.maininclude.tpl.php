<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:33:25
  from 'C:\TAOPIX\MediaAlbumWeb\templates\includes\maininclude.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa6725c52741_30700100',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '271d7614efa4d27b049567df95b9e26a953936e1' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\includes\\maininclude.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa6725c52741_30700100 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
?>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/autosuggest.css'),$_smarty_tpl);?>
" media="screen"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;
echo smarty_function_asset(array('file'=>'/css/cssmain_large.css'),$_smarty_tpl);?>
" media="screen"/>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/functions.js'),$_smarty_tpl);?>
"<?php if ((isset($_smarty_tpl->tpl_vars['nonce']->value))) {?> <?php echo $_smarty_tpl->tpl_vars['nonce']->value;
}?>><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/md5.js'),$_smarty_tpl);?>
"<?php if ((isset($_smarty_tpl->tpl_vars['nonce']->value))) {?> <?php echo $_smarty_tpl->tpl_vars['nonce']->value;
}?>><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/cookies.js'),$_smarty_tpl);?>
"<?php if ((isset($_smarty_tpl->tpl_vars['nonce']->value))) {?> <?php echo $_smarty_tpl->tpl_vars['nonce']->value;
}?>><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/autosuggest.js'),$_smarty_tpl);?>
"<?php if ((isset($_smarty_tpl->tpl_vars['nonce']->value))) {?> <?php echo $_smarty_tpl->tpl_vars['nonce']->value;
}?>><?php echo '</script'; ?>
><?php }
}
