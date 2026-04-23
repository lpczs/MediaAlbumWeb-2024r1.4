<?php
/* Smarty version 4.5.3, created on 2026-03-09 07:38:49
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderconfirmation_reorder_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ae790945fa91_72390789',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '296f7161d9a5b0e9ba89cb568da81bcba3175836' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderconfirmation_reorder_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:order/finalstageincludedecorator.tpl' => 1,
    'file:includes/googletagmanager.tpl' => 1,
    'file:order/orderconfirmation.tpl' => 1,
    'file:header_small.tpl' => 1,
  ),
),false)) {
function content_69ae790945fa91_72390789 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
if ($_smarty_tpl->tpl_vars['isajaxcall']->value == false) {?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderConfirmation');?>
</title>
        <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/functions.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/cookies.js'),$_smarty_tpl);?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;
echo smarty_function_asset(array('file'=>'/css/csscustomer_small.css'),$_smarty_tpl);?>
" media="screen"/>
        <?php $_smarty_tpl->_subTemplateRender("file:order/finalstageincludedecorator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>

        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
            //<![CDATA[

            <?php $_smarty_tpl->_subTemplateRender("file:order/orderconfirmation.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

            //]]>
        <?php echo '</script'; ?>
>

    </head>
    <body>
    <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
            dataLayer.push({ ecommerce: null });
            
            dataLayer.push({
                event: "purchase",
                ecommerce: {
                    transaction_id: "<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
",
                    value: <?php echo $_smarty_tpl->tpl_vars['orderdata']->value['total'];?>
,
                    tax: <?php echo $_smarty_tpl->tpl_vars['orderdata']->value['ordertaxtotal'];?>
,
                    shipping: <?php echo $_smarty_tpl->tpl_vars['orderdata']->value['shippingtotal'];?>
,
                    currency: "<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['ordercurrency'];?>
",
                    
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderdata']->value['orderlines'], 'line');
$_smarty_tpl->tpl_vars['line']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['line']->value) {
$_smarty_tpl->tpl_vars['line']->do_else = false;
?>
                    
                    items: [
                        {
                            item_id: "<?php echo $_smarty_tpl->tpl_vars['ordernumber']->value;?>
",
                            item_name: "<?php echo $_smarty_tpl->tpl_vars['line']->value['productname'];?>
",
                            affiliation: "<?php echo $_smarty_tpl->tpl_vars['orderdata']->value['brandcode'];?>
",
                            price: <?php echo $_smarty_tpl->tpl_vars['line']->value['price'];?>
,
                            quantity: "<?php echo $_smarty_tpl->tpl_vars['line']->value['qty'];?>
"
                        },
                        
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        
                    ]
                }
            });
        <?php echo '</script'; ?>
>
    
    <?php }?>
        <div id="headerSmall" class="header">
            <div class="headerinside">
                <?php $_smarty_tpl->_subTemplateRender("file:header_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
        </div>

<?php }?>
        <div id="contentConfirmation" class="contentScrollCart">

            <div class="contentVisible">

                <div class="pageLabel">

                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderConfirmation');?>


                </div>

                <div class="orderInformationBloc outerBox outerBoxPadding">
                    <div>
                        <?php echo $_smarty_tpl->tpl_vars['str_MessageOrderConfirmation1']->value;?>

                    </div>

                    <?php if ($_smarty_tpl->tpl_vars['cciCompletionMessage']->value != '') {?>

                        <div class="orderInformationMessage">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderConfirmation3');?>

                        </div>

                        <div class="orderInformationMessage">
                            <?php echo $_smarty_tpl->tpl_vars['cciCompletionMessage']->value;?>

                        </div>

					<?php } elseif ($_smarty_tpl->tpl_vars['additionalPaymentinfo']->value != '') {?>

						<div class="orderInformationMessage">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderConfirmation3');?>

                        </div>

						<div class="orderInformationMessage">
							<?php echo $_smarty_tpl->tpl_vars['additionalPaymentinfo']->value;?>

						</div>

                    <?php } else { ?>

                        <div class="orderInformationMessage">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderConfirmation3');?>

                        </div>

                    <?php }?> 
                </div>

                <?php if ($_smarty_tpl->tpl_vars['mainwebsiteurl']->value != '') {?>

                <div class="buttonBottomSection">

                    <div data-decorator="fnRedirect" data-url="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['mainwebsiteurl']->value, ENT_QUOTES, 'UTF-8', true);?>
">
                        <div class="btnAction btnContinue">
                            <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                        </div>
                    </div>

                </div>
                <div class="clear"></div>

                <?php }?>
            </div> <!-- contentVisible -->

        </div> <!-- contentScrollCart -->

<?php if ($_smarty_tpl->tpl_vars['isajaxcall']->value == false) {?>

    </body>

</html>

<?php }
}
}
