<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:13:16
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\finalstageincludedecorator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa626cb65bc3_71377213',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a35d98dd0afeba39bb70dc10a47ec58d185a4044' => 
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
function content_69aa626cb65bc3_71377213 (Smarty_Internal_Template $_smarty_tpl) {
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
