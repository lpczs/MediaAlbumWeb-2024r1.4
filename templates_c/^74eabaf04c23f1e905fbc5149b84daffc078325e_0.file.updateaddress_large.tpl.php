<?php
/* Smarty version 4.5.3, created on 2026-03-06 08:29:58
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\updateaddress_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa9086722931_64038487',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '74eabaf04c23f1e905fbc5149b84daffc078325e' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\updateaddress_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
    'file:order/updateaddress.tpl' => 1,
    'file:header_large.tpl' => 1,
    'file:addressform.tpl' => 1,
  ),
),false)) {
function content_69aa9086722931_64038487 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="csrf-token" content="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
         <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        //<![CDATA[
            <?php $_smarty_tpl->_subTemplateRender("file:order/updateaddress.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        //]]>
        <?php echo '</script'; ?>
>
    </head>
        <!--[if IE 6]><body onload="initializeAddress(false, '');" style="position: relative" class="ie6"><![endif]-->
        <!--[if gt IE 6]><!-->
    <body style="position: relative">
        <!--<![endif]-->
		<div id="loadingBox" class="section maw_dialog">
			<div class="dialogTop">
					<h2 id="loadingTitle" class="title-bar"></h2>
			</div>
			<div class="content">
				<div class="loadingMessage">
					<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />
				</div>
			</div>
		</div>
		<div id="shimLoading">&nbsp;</div>
        <div id="outerPage" class="outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
            <div id="header" class="headertop">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender("file:header_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
            </div>
<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarleft']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}?>
            <div id="contentHolder" class="longContentHeader">
                <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '' && $_smarty_tpl->tpl_vars['sidebarsearch']->value == '') {?>class="fullsizepage"<?php }?>>
                    <div id="page" class="section">
                        <h1 class="title-bar">
                            <?php echo $_smarty_tpl->tpl_vars['title']->value;?>

                        </h1>
                        <div class="content">
							<?php $_smarty_tpl->_subTemplateRender("file:addressform.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                        </div>
                    </div>
                </div>
                <div class="buttonBottom">
<?php if ($_smarty_tpl->tpl_vars['useraddressupdated']->value == 1) {?>
                    <div class="btnLeft">
                        <div class="contentBtn" id="cancel">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                            <div class="btn-red-right"></div>
                        </div>
                    </div>
<?php }?>
                    <div class="btnRight">
                        <div class="contentBtn" id="ok">
                            <div class="btn-green-left" ></div>
                            <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>
</div>
                            <div class="btn-accept-right"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
<?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value != '' || $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                <div class="side-outer-panel">
    <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value != '') {?>
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaraccount']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
    <?php }?>
	<?php if ($_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarcontactdetails']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
    <?php }?>
                </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value != '') {?>
                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}?>
            </div>
        </div>
        <form id="submitformaddress" name="submitformaddress" method="post" accept-charset="utf-8" action="#">
            <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
            <input type="hidden" name="shippingcfscontact" value="<?php echo $_smarty_tpl->tpl_vars['shippingcfscontact']->value;?>
"/>
            <input type="hidden" id="contactfname" name="contactfname" />
            <input type="hidden" id="contactlname" name="contactlname" />
            <input type="hidden" id="companyname" name="companyname" />
            <input type="hidden" id="address1" name="address1" />
            <input type="hidden" id="address2" name="address2" />
            <input type="hidden" id="address3" name="address3" />
            <input type="hidden" id="address4" name="address4" />
            <input type="hidden" id="add41" name="add41" />
            <input type="hidden" id="add42" name="add42" />
            <input type="hidden" id="add43" name="add43" />
            <input type="hidden" id="city" name="city" />
            <input type="hidden" id="county" name="county" />
            <input type="hidden" id="state" name="state" />
            <input type="hidden" id="regioncode" name="regioncode" />
            <input type="hidden" id="region" name="region" />
            <input type="hidden" id="postcode" name="postcode" />
            <input type="hidden" id="countrycode" name="countrycode" />
            <input type="hidden" id="countryname" name="countryname" />
            <input type="hidden" id="submit_telephonenumber" name="telephonenumber" />
            <input type="hidden" id="submit_email" name="email" />
            <input type="hidden" id="submit_registeredtaxnumbertype" name="registeredtaxnumbertype" />
            <input type="hidden" id="submit_registeredtaxnumber" name="registeredtaxnumber" />
            <input type="hidden" name="previousstage" value="<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
"/>
            <input type="hidden" name="stage" value="<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
"/>
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        </form>
    </body>
</html><?php }
}
