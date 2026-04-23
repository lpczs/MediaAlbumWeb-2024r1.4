<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:35:26
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\\ubbpro\templates\order\orderconfirmation_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb5e73e256_40443645',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '53ed5beb2fbbeadd324222771c03c4e48577b983' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\\\ubbpro\\templates\\order\\orderconfirmation_large.tpl',
      1 => 1649493092,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
    'file:order/finalstageincludedecorator.tpl' => 1,
    'file:order/orderconfirmation.tpl' => 1,
    'file:header_large.tpl' => 1,
  ),
),false)) {
function content_69b4bb5e73e256_40443645 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderConfirmation');?>
</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>

        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php $_smarty_tpl->_subTemplateRender("file:order/finalstageincludedecorator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

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
        <div class="outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
            <div id="header" class="headerScroll">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender("file:header_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
            </div>
            <div class="contentNavigation <?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-navigation<?php }?>">
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                <div class="contentNavigationImageCompanion">
	<?php } else { ?>
                <div class="contentNavigationImage">
	<?php }?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	<?php }?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationBloc">
                        <div class="navigationActiveLeft"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                <div class="contentNavigationTextCompanion">
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationCompanionSelection');?>
</div>
	<?php } else { ?>
                <div class="contentNavigationText">
	<?php }?>
					<div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationCart');?>
</div>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationShippingBilling');?>
</div>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationPayment');?>
</div>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationConfirmation');?>
</div>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="contentScroll" class="contentScrollCart">
                            <div id="contentHolder">
                    <div id="pageFooterHolder" class="fullsizepage">
                        <div id="page" class="section">
                            <div class="title-bar">
                                <h2 class="title-confirmation">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderConfirmation');?>

                                </h2>
                            </div>
                            <div class="content-footer">
                                <div class="confirmationBoldText">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderConfirmation2');?>

                                </div>
                                <div class="confirmationMessage">
                                    <?php echo $_smarty_tpl->tpl_vars['str_MessageOrderConfirmation1']->value;?>

                                </div>
                                <div class="confirmationMessage">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderConfirmation3');?>

                                </div>

                                <?php if ($_smarty_tpl->tpl_vars['cciCompletionMessage']->value != '') {?>
                                    <div class="confirmationMessage">
                                        <?php echo $_smarty_tpl->tpl_vars['cciCompletionMessage']->value;?>

                                    </div>
                                <?php }?>

								<?php if ($_smarty_tpl->tpl_vars['additionalPaymentinfo']->value != '') {?>
									<div class="confirmationMessage">
										<?php echo $_smarty_tpl->tpl_vars['additionalPaymentinfo']->value;?>

									</div>
								<?php }?>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['mainwebsiteurl']->value != '') {?>
                                <div class="contentDottedImage"></div>
                                <div class="btnRightContinue">
                                    <div class="contentBtn" data-decorator="fnRedirect" data-url="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['mainwebsiteurl']->value, ENT_QUOTES, 'UTF-8', true);?>
">
                                        <div class="btn-green-left" ></div>
                                        <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                                        <div class="btn-accept-right"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php }
}
