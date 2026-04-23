<?php
/* Smarty version 4.5.3, created on 2026-03-07 15:48:01
  from 'C:\TAOPIX\MediaAlbumWeb\templates\browsernotsupported.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ac48b1c8e7a0_24611421',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bf46cde6f1a594cc9efeeb61ff005787466391f4' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\browsernotsupported.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
  ),
),false)) {
function content_69ac48b1c8e7a0_24611421 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
</title>

		<?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
		
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    </head>
    <body>
         <div id="header" class="headertop">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>
		<div class="unsupported-browser-container">
			<h1><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_titleBrowserNotSupported');?>
</h1>
			<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_titleRecommendBrowsers');?>
</p>

			<div class="panel-wrap">
				
				<?php if (!$_smarty_tpl->tpl_vars['hidechrome']->value) {?>	
					<a class="browser-box" href="http://www.google.com/chrome">
						<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/chrome.png" alt="Chrome" />
						<h3><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelBrowserChrome');?>
</h3>
						<p><?php echo $_smarty_tpl->tpl_vars['minchrome']->value;?>
</p>
						<?php if (!$_smarty_tpl->tpl_vars['hidechromedownload']->value) {?>
							<div class="link-wrap">
								<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDownload');?>
</p>
							</div>
						<?php }?>
					</a>		
				<?php }?>
				
				<?php if (!$_smarty_tpl->tpl_vars['hidefirefox']->value) {?>
					<a class="browser-box" href="http://www.mozilla.org">
						<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/firefox.png" alt="Firefox" />
						<h3><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelBrowserFirefox');?>
</h3>
						<p><?php echo $_smarty_tpl->tpl_vars['minff']->value;?>
</p>
						<?php if (!$_smarty_tpl->tpl_vars['hidefirefoxdownload']->value) {?>
							<div class="link-wrap">
								<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDownload');?>
</p>
							</div>
						<?php }?>
					</a>
				<?php }?>

				<?php if (!$_smarty_tpl->tpl_vars['hidesafari']->value) {?>
					<div class="browser-box">
						<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/safari.png" alt="Safari" />
						<h3><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelBrowserSafari');?>
</h3>
						<p><?php echo $_smarty_tpl->tpl_vars['minsafari']->value;?>
</p>
					</div>
				<?php }?>

				<?php if (!$_smarty_tpl->tpl_vars['hideedge']->value) {?>
					<a class="browser-box" href="https://www.microsoft.com/en-gb/windows/microsoft-edge">
						<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/edge.png" alt="Edge" />
						<h3><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelBrowserEdge');?>
</h3>
						<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAllVersions');?>
</p>
						<?php if (!$_smarty_tpl->tpl_vars['hideedgedownload']->value) {?>
							<div class="link-wrap">
								<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDownload');?>
</p>
							</div>
						<?php }?>					
					</a>
				<?php }?>
			</div>
		</div>
    </body>
</html><?php }
}
