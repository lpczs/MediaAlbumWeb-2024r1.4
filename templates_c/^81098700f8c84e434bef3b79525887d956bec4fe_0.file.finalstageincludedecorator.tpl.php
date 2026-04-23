<?php
/* Smarty version 4.5.3, created on 2026-03-23 02:10:03
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\finalstageincludedecorator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69c0a0fb1fc7e4_90833456',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '81098700f8c84e434bef3b79525887d956bec4fe' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\finalstageincludedecorator.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c0a0fb1fc7e4_90833456 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/listeners.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>

    window.addEventListener('DOMContentLoaded', function(event) {
        document.body.addEventListener('click', decoratorListener);
    });

    // Wrapper for window redirection.
    function fnRedirect(pElement)
    {
        window.location.replace(pElement.getAttribute('data-url'));
    }

<?php echo '</script'; ?>
><?php }
}
