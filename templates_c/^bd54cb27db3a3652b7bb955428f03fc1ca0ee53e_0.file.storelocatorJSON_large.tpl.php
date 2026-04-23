<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:59
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\storelocatorJSON_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb43bd3ad2_33834769',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bd54cb27db3a3652b7bb955428f03fc1ca0ee53e' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\storelocatorJSON_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb43bd3ad2_33834769 (Smarty_Internal_Template $_smarty_tpl) {
?>{
    "storeCode": "<?php echo $_smarty_tpl->tpl_vars['storecode']->value;?>
",
    "payInStoreOption": <?php echo $_smarty_tpl->tpl_vars['payInStoreAllowed']->value;?>
,
    "showAllCode": "<?php echo $_smarty_tpl->tpl_vars['showAllCode']->value;?>
",
    "countryCode": "<?php echo $_smarty_tpl->tpl_vars['countrylist']->value[0]['code'];?>
",
    "searchCountry": "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['country'];?>
",
    "searchRegion": "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['region'];?>
",
    "searchStoreGroup": "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['storeGroup'];?>
",
    "searchText": "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['filter'];?>
",
    "privateSearchText": "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['privateFilter'];?>
",
    "showcountrylist": <?php echo $_smarty_tpl->tpl_vars['showcountrylist']->value;?>
,
    "storeLoacationData": <?php echo $_smarty_tpl->tpl_vars['storelocationdata']->value;?>

}<?php }
}
