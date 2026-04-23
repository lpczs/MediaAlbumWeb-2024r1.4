<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:33:27
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\home\home.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa6727211464_57372322',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e44e53bbe41a92f274638985e91e67704455f2b6' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\home\\home.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa6727211464_57372322 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		layout: 'fit',
		anchor: '100% 100%',
		html: '<iframe src="https://home.taopix.com?wvs=<?php echo $_smarty_tpl->tpl_vars['webversionstring']->value;?>
" title="description"  height="100%" width="100%" "allow-scripts allow-same-origin allow-popups allow-top-navigation-by-user-activation allow-forms"></iframe>',
		baseParams: { ref: "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
} 


/* close this window panel */
function windowClose()
{
	if (Ext.getCmp('MainWindow'))
	{
		centreRegion.remove('MainWindow', true);
		centreRegion.doLayout();
	}	
}


<?php }
}
