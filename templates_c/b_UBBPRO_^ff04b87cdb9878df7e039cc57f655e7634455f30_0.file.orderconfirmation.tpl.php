<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:35:27
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderconfirmation.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb5f098103_79274973',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ff04b87cdb9878df7e039cc57f655e7634455f30' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderconfirmation.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb5f098103_79274973 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['orderdata']->value['googleanalyticscode'] != '') {?>

    

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['googleanalyticscode'];?>
', 'auto');
	
	<?php if ($_smarty_tpl->tpl_vars['orderdata']->value['googleanalyticsuseridtracking']) {?>
		
		ga('set', 'userId', <?php echo $_smarty_tpl->tpl_vars['orderdata']->value['userid'];?>
); // Set the user ID using signed-in userid.
		
	<?php }?>
	
    ga('send', 'pageview');
    ga('require', 'ecommerce', 'ecommerce.js');

    ga('ecommerce:addTransaction', {
    'id': '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
',	// Transaction ID. Required
    'affiliation': '<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['brandcode'];?>
', // Affiliation or store name
    'revenue': '<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['total'];?>
', // Grand Total
    'shipping': '<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['shippingtotal'];?>
', // Shipping
    'tax': '<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['ordertaxtotal'];?>
', // Tax
    'currency': '<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['ordercurrency'];?>
' // Currency code
    });

    

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderdata']->value['orderlines'], 'line');
$_smarty_tpl->tpl_vars['line']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['line']->value) {
$_smarty_tpl->tpl_vars['line']->do_else = false;
?>
        // add item might be called for every item in the shopping cart
        // loop through each item in the cart and
        // print out addItem for each
        
            ga('ecommerce:addItem', {
            'id': '<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
', // Transaction ID. Required
            'name': '<?php echo $_smarty_tpl->tpl_vars['line']->value['productname'];?>
', // Product name. Required
            'sku': '<?php echo $_smarty_tpl->tpl_vars['line']->value['productcode'];?>
', // SKU/code
            'category': '', // Category or variation
            'price': '<?php echo $_smarty_tpl->tpl_vars['line']->value['price'];?>
', // Unit price
            'quantity': '<?php echo $_smarty_tpl->tpl_vars['line']->value['qty'];?>
' // Quantity
            });
        

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    ga('ecommerce:send'); //submits transaction to the Analytics servers

<?php }?>

// stop the back button from working to prevent the user from being taken back into the shopping cart
history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    }; <?php }
}
