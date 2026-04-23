<?php
/* Smarty version 4.5.3, created on 2026-03-12 10:50:56
  from 'C:\TAOPIX\MediaAlbumWeb\templates\includes\customerinclude.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b29a9080c6b2_98348025',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e75dba4569a6b1835c8725440f999f8ba205b98a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\includes\\customerinclude.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b29a9080c6b2_98348025 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
if ($_smarty_tpl->tpl_vars['faviconpath']->value != '') {?>
    <link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['faviconpath']->value;?>
" type="image/x-icon" />
<?php }?>

<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/autosuggest.css'),$_smarty_tpl);?>
" media="screen"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/lightboxpaymentdialog.css'),$_smarty_tpl);?>
" media="screen"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/responsivedialog.css'),$_smarty_tpl);?>
" media="screen"/>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/functions.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/md5.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/cookies.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/autosuggest.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/zxcvbn.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/listeners.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/responsiveDialog.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
><?php }
}
