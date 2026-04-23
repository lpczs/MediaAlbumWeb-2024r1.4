<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:33:22
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\admin.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa672258a910_97929631',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '00a56887df12d58edcac7e9d4fa9289ad5255161' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\admin.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/extjsinclude.tpl' => 1,
    'file:includes/maininclude.tpl' => 1,
  ),
),false)) {
function content_69aa672258a910_97929631 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),1=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
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
 - Control Centre</title>
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/admin.css'),$_smarty_tpl);?>
" />
                <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/layout-browser.css" />
        <?php echo '<script'; ?>
 type="text/javascript">
            //<![CDATA[
            var gLabelPricingTab = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPricing');?>
";
            var gLabelLicenseKeyTab = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleLicenseKeys');?>
";
            var gLabelPriceDescriptionTab = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelClientPriceDescription');?>
";
            var gLabelAdditionalInfoTab = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalInformation');?>
";
            var gTextFieldBlank = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
";
            var gLabelLanguageName = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
";
            var gLabelInformation = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
";
            var gLabelSelectLanguage = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
";
            var gExtJsTypeValue = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
";
            var gLabelCode = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
";
            var gLabelStatus = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
";
            var gLabelName = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
";
            var gLabelSelectCountry = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectCountry');?>
";
            var gLabelPriceList = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceList');?>
";
            var gLabelFixedQuantityRanges = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFixedQuantityRanges');?>
";
            var gLabelInheritParentQty = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInheritParentQty');?>
";
            var gLabelUseExternalShoppingCart = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUseExternalShoppingCart');?>
";
            var gLabelDefault = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefault');?>
";
            var gLabelTaxRate = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPricingIncludesTax');?>
";
            var gLabelActive = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
            var gLabelInactive = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
";
            var gLabelConfirmation = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
";
            var gMessageConfirmUnlockUser = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmUnlockUser');?>
";
            var gButtonUnlock = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUnlock');?>
";
            var gMessageLoading = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
";
            var gLangCode = "<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
";

            //]]>
        <?php echo '</script'; ?>
>

        <?php $_smarty_tpl->_subTemplateRender("file:includes/extjsinclude.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php $_smarty_tpl->_subTemplateRender("file:includes/maininclude.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php echo '<script'; ?>
 type="text/javascript">
            //<![CDATA[
            accordianWindowInitialized = false;
            var gIsDashboard = false;

            var sessionId = '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
';
            var buildInfo = "<?php echo $_smarty_tpl->tpl_vars['buildversionstring']->value;?>
";
			var gAdminAuthentificationEnabled = <?php echo $_smarty_tpl->tpl_vars['adminauthentificationenabled']->value;?>
;
            var reactPanel = null;
            var userId = '<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
';

            Ext.MessageBox.minWidth = 300;

            

            var itemLoaded = function()
            {
                initialize();
                if (Ext.get('centreRegionPanel')) Ext.get('centreRegionPanel').unmask();
            };

            var itemClicked = function(panel)
            {
                if (panel instanceof HTMLAnchorElement)
                {
                   if (reactPanel)
                    {
                        reactPanel.hide();
                        reactPanel = null;
                    }
                }

                Ext.get('centreRegionPanel').mask('<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/loading.gif" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsAlertLoading');?>
" /> <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsAlertLoading');?>
','loadingShim');

                if (Ext.getCmp('MainWindow'))
                {
                    windowClose();
                    accordianWindowInitialized = false;
                }

                if (typeof uuid !== undefined)
                {
                  uuid = 0;
                }

                var method = '';

                if ((panel.getAttribute) && (panel.getAttribute('method')))
                {
                    method = panel.getAttribute('method');
                }
                else
                {
                    method = 'initialize';
                }

                var action = '';

                if ((panel.getAttribute) && (panel.getAttribute('faction')))
                {
                    action = panel.getAttribute('faction');
                }
                else
                {
                    action = panel.faction;
                }

                var thisIsReact = false;

                if ((panel.getAttribute) && (panel.getAttribute('isreact')))
                {
                    thisIsReact = true;
                }

                if (thisIsReact)
                {
                    var shadowDom = document.getElementById('shadow').shadowRoot;

                    // Remove old elements, apart from the style tags.
                    var nonFixedElements = Object.values(shadowDom.children).filter(function(node)
                    {
                        return ! node.dataset.shadow;
                    });

                    var nonFixedElementsLength = nonFixedElements.length;

                    if (nonFixedElementsLength > 0) 
                    {
                        for (var i = 0; i < nonFixedElementsLength; i++)
                        {
                        nonFixedElements[i].remove();
                        }
                    }

                    var type = 0;

                    if (typeof panel.type !== undefined && panel.type !== '')
                    {
                        type = panel.type;
                    }

                    reactPanel = window.Router().get(action, {type: parseInt(type), userId: userId, sessionRef: parseInt(sessionId, 10), documentRoot: shadowDom, container: shadowDom});
                    reactPanel.display();

                   if (Ext.get('centreRegionPanel')) Ext.get('centreRegionPanel').unmask();

                    if (Ext.getCmp('MainWindow'))
                    {
                        windowClose();
                    }

                    return false;
                }
                else
                {
                    if (!accordianWindowInitialized)
                    {
                        accordianWindowInitialized = true;
                        Ext.taopix.loadJavascript(centreRegion, '', 'index.php?fsaction=' + action + '.' + method + '&ref='+sessionId, [], '', 'itemLoaded', false);
                    }
                }
            };

            var menuOnExpand = function(panel, animate)
            {
                if (typeof panel.isreact !== undefined && panel.isreact == true)
                {
                  if (Ext.getCmp('MainWindow'))
                  {
                      windowClose();
                      accordianWindowInitialized = false;
                  }

                  var shadowDom = document.getElementById('shadow').shadowRoot;

                  // Remove old elements, apart from the style tags.
                  var nonFixedElements = Object.values(shadowDom.children).filter(function(node)
                  {
                    return ! node.dataset.shadow;
                  });

                  var nonFixedElementsLength = nonFixedElements.length;

                  if (nonFixedElementsLength > 0) 
                  {
                    for (var i = 0; i < nonFixedElementsLength; i++)
                    {
                      nonFixedElements[i].remove();
                    }
                  }
                  
                  reactPanel = window.Router().get(panel.faction, {sessionRef: parseInt(sessionId, 10), userId: userId, documentRoot: shadowDom, container: shadowDom});
                  reactPanel.display();
                  return false;
                }
                else
                {
                    if (reactPanel)
                    {
                        reactPanel.hide();
                        reactPanel = null;
                    }

                    if (panel.hasItems == false)
                    {
                        itemClicked(panel);
                        return false;
                    }
                }   
            };

            var menuOnBeforeRender = function(panel)
            {
                if (panel.hasItems == false)
                {
                    /* hide expand/collapse icon
                    Ext.apply(panel, {collapsible: false}); */
                }
            };

            Ext.onReady(function(){
                Ext.QuickTips.init();
                Ext.form.Field.prototype.msgTarget = 'side';

                logOut = function()
                {

                    Ext.Ajax.request(
					{
						url: '?fsaction=Admin.logout',
						params:
						{
							ref:sessionId
						}
					});

                    window.location = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
";
                };

                var bulkEditorTab = new Ext.Panel({
                    name: 'bulkeditor',
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleBulkConfiguration');?>
",
                    hasItems: false,
                    iconCls: 'silk-application-view-detail',
                    collapsed: false,
                    faction: 'AdminExperienceEditingOverview',
                    isreact: true,
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var experienceTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleOnlineExperiences');?>
",
                    hasItems: true,
                    iconCls: 'silk-application-osx',
                    html: "<ul class='submenu' id='experienceTab'><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/application_view_tile.png' alt='' /><a href='#' isreact='true' faction='AdminExperienceTheme'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleUIThemes');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/application_side_boxes.png' alt='' /><a href='#' isreact='true' type='0' faction='AdminExperienceEditing'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleUIConfigurations');?>
</a></li></ul>",
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var connectorsTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleConnectors');?>
",
                    hasItems: false,
                    iconCls: 'tpx-shopify-connector',
                    collapsed: false,
                    faction: 'AdminConnectors',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var aboutTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleAbout');?>
",
                    hasItems: false,
                    iconCls: 'silk-help',
                    collapsed: false,
                    faction: 'AdminAbout',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var homeTab = new Ext.Panel({
                    title: "Home",
                    hasItems: false,
                    iconCls: 'silk-home',
                    collapsed: false,
                    faction: 'AdminHome',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var constantsTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleConstants');?>
",
                    iconCls: 'silk-report',
                    hasItems: false,
                    faction: 'AdminConstants',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var currenciesTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleCurrencies');?>
",
                    iconCls: 'silk-money-dollar',
                    hasItems: false,
                    faction: 'AdminCurrencies',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var usersTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleUsers');?>
",
                    hasItems: false,
                    iconCls: 'silk-user-suit',
                    faction: 'AdminUsers',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var taxTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleTax');?>
",
                    hasItems: true,
                    iconCls: 'silk-coins',
                            html: "<ul class='submenu' id='taxTab'><?php if (!$_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/coins.png' alt='' /><a href='#' faction='AdminTaxRates'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TaxTitleTaxRates');?>
</a></li><?php }?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/coins.png' alt='' /><a href='#' faction='AdminTaxZones'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TaxTitleTaxZones');?>
</a></li></ul>",
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var dataExportTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleExport');?>
",
                    hasItems: true,
                    iconCls: 'silk-server-go',
                    faction: 'AdminExportManual',
                    html: "<ul class='submenu'><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/server_go.png' alt='' /><a href='#' faction='AdminExportManual'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExportTitleManual');?>
</a></li><?php if ($_smarty_tpl->tpl_vars['TPX_LOGIN_SYSTEM_ADMIN']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/server_go.png' alt='' /><a href='#' faction='AdminExportEvent'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExportTitleEvent');?>
</a></li><?php }?></ul>",
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var multiSiteTab = new Ext.Panel({
                    
                    <?php if ($_smarty_tpl->tpl_vars['optionMS']->value) {?>
                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleMultiSite');?>
",
                    <?php } else { ?>
                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleSites');?>
",
                    <?php }?>
                    
                    hasItems: true,
                    iconCls: 'silk-sitemap-color',
                    html: "<ul class='submenu'><?php if ($_smarty_tpl->tpl_vars['optionMC']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/sitemap_color.png' alt='' /><a href='#' faction='AdminSitesCompanies'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteCompanies');?>
</a></li><?php }?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/sitemap_color.png' alt='' /><a href='#' faction='AdminSitesSitesAdmin'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteAdmin');?>
</a></li><?php if ($_smarty_tpl->tpl_vars['optionCFS']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/sitemap_color.png' alt='' /><a href='#' faction='AdminSitesSiteGroups'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_StoreGroups');?>
</a></li><?php }
if ($_smarty_tpl->tpl_vars['optionMS']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/sitemap_color.png' alt='' /><a href='#' faction='AdminSitesOrderRouting'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteOrderRouting');?>
</a></li><?php }?></ul>",
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var productsTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleProducts');?>
",
                    hasItems: false,
                    iconCls: 'silk-book',
                    cls : 'notools',
                    faction:'AdminProducts',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var productGroupsTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleProductGroups');?>
",
                    hasItems: false,
                    faction:'AdminProductGroups',
                    iconCls: 'silk-book-link',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                })

                var shippingTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleShipping');?>
",
                    hasItems: true,
                    iconCls: 'silk-lorry',
                    html: "<ul class='submenu'><?php if (!$_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/lorry.png'><a href='#' faction='AdminShippingMethods'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ShippingTitleShippingMethods');?>
</a></li><?php }?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/lorry.png'><a href='#' faction='AdminShippingZones'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ShippingTitleShippingZones');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/lorry.png'><a href='#' faction='AdminShippingRates'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ShippingTitleShippingRates');?>
</a></li></ul>",
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var paymentMethodsTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitlePaymentMethods');?>
",
                    hasItems: false,
                    cls : 'notools',
                    faction:'AdminPaymentMethods',
                    iconCls: 'silk-creditcards',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var customersTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleCustomers');?>
",
                    hasItems: false,
                    faction:'AdminCustomers',
                    cls : 'notools',
                    iconCls: 'silk-group',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var vouchersTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleVouchersGiftCards');?>
",
                    hasItems: true,
                    iconCls: 'silk-tag-pink',
                    html: "<ul class='submenu'><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/tag_pink.png' alt=''><a href='#' faction='AdminVouchers' method='displayList'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_VoucherTitleSingleVouchers');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/tag_pink.png' alt=''><a href='#' faction='AdminVouchersPromotion'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_VoucherTitleVoucherPromotions');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/tag_pink.png' alt=''><a href='#' faction='AdminGiftCards' method='displayList'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_VoucherTitleGiftCards');?>
</a></li></ul>",
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });


                var brandingTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleBranding');?>
",
                    cls : 'notools',
                    hasItems: false,
                    faction:'AdminBranding',
                    iconCls: 'silk-layout-header',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var autoUpdateTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleAutoUpdate');?>
",
                    hasItems: true,
                    html: "<ul class='submenu'><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a faction='AdminAutoUpdate' method='initializeApplication' href='#'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleApplication');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a href='#' faction='AdminAutoUpdate' method='initializeLicenseKeys'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleLicenseKeys');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a href='#' faction='AdminAutoUpdate' method='initializeProducts'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleProductCollections');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a href='#' faction='AdminAutoUpdate' method='initializeBackgrounds'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleBackgrounds');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a href='#' faction='AdminAutoUpdate' method='initializeMasks'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleMasks');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a href='#' faction='AdminAutoUpdate' method='initializeScrapbookPictures'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleScrapbook');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/world_go.png' alt=''><a href='#' faction='AdminAutoUpdate' method='initializeFrames'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleFrames');?>
</a></li></ul>",
                    iconCls: 'silk-world-go',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var componentsTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleComponents');?>
",
                    hasItems: false,
                    iconCls: 'silk-bricks',
                    faction:'AdminComponentCategories',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                <?php if (!$_smarty_tpl->tpl_vars['optionHOLDES']->value) {?>
                var dataRetentionPoliciesTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleManagementPolicies');?>
",
                    hasItems: false,
                    iconCls: 'silk-database-delete',
                    faction:'AdminDataRetentionAdmin',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                 });
                <?php }?>

                var collectFromStoreTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCollectFromStore');?>
",
                    hasItems: false,
                    iconCls: 'silk-basket-put',
                    faction:'CollectFromStore',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var scheduledTasksTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleScheduledTasks');?>
",
                    hasItems: true,
                    html: "<ul class='submenu'><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/time.png' alt='' /><a faction='AdminScheduledTasks' href='#'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleScheduledTasks');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/time.png' alt='' /><a href='#' faction='AdminScheduledEvents'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleScheduledEvents');?>
</a></li></ul>",
                    iconCls: 'silk-time',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var matadataTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleMetaData');?>
",
                    hasItems: true,
                    html: "<ul class='submenu'><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/attach.png' alt='' /><a faction='AdminMetadataKeywords' href='#'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleMetaDataKeyWords');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/attach.png' alt='' /><a href='#' faction='AdminMetadataKeywordsGroups'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleMetaDataKeyWordGroups');?>
</a></li></ul>",
                    iconCls: 'silk-attach',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var taopixonlineTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleTAOPIXOnline');?>
",
                    hasItems: true,
                    html: "<ul class='submenu'><?php if (!$_smarty_tpl->tpl_vars['optionHOLDES']->value) {?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/database_gear.png' alt='' /><a faction='AdminTaopixOnlineImageServersAdmin' href='#'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleServerManagement');?>
</a></li><?php }?><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/link_go.png' alt='' /><a faction='AdminTaopixOnlineProductURLAdmin' href='#'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleProductURLs');?>
</a></li><li><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/font.png' alt='' /><a faction='AdminTaopixOnlineFontLists' href='#'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleFontLists');?>
</a></li></ul>",
                    iconCls: 'silk-connect',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var productionTab = new Ext.Panel({
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduction');?>
",
                    hasItems: false,
                    iconCls: 'silk-printer',
                    faction:'AdminProduction',
                    cls : 'notools',
                    listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });

                var oauthProviderTab = new Ext.Panel({
                  title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOAuth2Providers');?>
",
                  hasItems: false,
                  iconCls: 'tpx-oauth-provider',
                  faction: 'AdminOAuthProvider',
                  cls : 'notools',
                  listeners: { 'beforeexpand': menuOnExpand, 'beforerender': menuOnBeforeRender }
                });


                var accordion = new Ext.Panel({
                    region:'west',
                    width: <?php echo (($tmp = $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'kGUIAdminAccordionWidth') ?? null)===null||$tmp==='' ? 50 ?? null : $tmp);?>
,
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Title');?>
",
                    collapsible: true,
                    autoScroll: true,
                    id: 'accordionPanel',
                    
                    <?php if ($_smarty_tpl->tpl_vars['TPX_LOGIN_PRODUCTION_USER']->value) {?>
                        collapsed:true,
                    <?php }?>
                    
                    layout: { type: 'accordion', animate: true , fill:false},
                    items: [
                        
                        <?php if ($_smarty_tpl->tpl_vars['TPX_LOGIN_SYSTEM_ADMIN']->value) {?>
                            homeTab,
                            aboutTab,
                            constantsTab,
                            currenciesTab,
                            usersTab,
                            taxTab,
                            dataExportTab,
                            <?php if ($_smarty_tpl->tpl_vars['adminsitesenabled']->value) {?>
                                multiSiteTab,
                            <?php }?>
                            productsTab,
                            productGroupsTab,
                            shippingTab,
                            paymentMethodsTab,
                            customersTab,
                            vouchersTab,
                            brandingTab,
                            autoUpdateTab,
                            componentsTab,
                            scheduledTasksTab,
                            matadataTab,
                            oauthProviderTab
                            <?php if ($_smarty_tpl->tpl_vars['optionDESOL']->value && !$_smarty_tpl->tpl_vars['optionHOLDES']->value) {?>
                            ,dataRetentionPoliciesTab
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['optionDESOL']->value) {?>
                            ,taopixonlineTab
                            ,experienceTab
                            ,bulkEditorTab
                            <?php }?>
                            ,productionTab
                            <?php if ($_smarty_tpl->tpl_vars['optionsSCNTR']->value) {?>
                            ,connectorsTab
                            <?php }?>
                        <?php } elseif ($_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value) {?>
                            usersTab,
                            taxTab,
                            dataExportTab,
                            productsTab,
                            productGroupsTab,
                            componentsTab,
                            shippingTab,
                            customersTab,
                            vouchersTab,
                            brandingTab,
                            autoUpdateTab
                        <?php } elseif ($_smarty_tpl->tpl_vars['TPX_LOGIN_SITE_ADMIN']->value) {?>
                            usersTab
                        <?php } elseif ($_smarty_tpl->tpl_vars['TPX_LOGIN_BRAND_OWNER']->value) {?>
                            dataExportTab
                        <?php } elseif (($_smarty_tpl->tpl_vars['TPX_LOGIN_STORE_USER']->value || $_smarty_tpl->tpl_vars['TPX_LOGIN_DISTRIBUTION_CENTRE_USER']->value)) {?>
                            collectFromStoreTab
                        <?php } elseif ($_smarty_tpl->tpl_vars['TPX_LOGIN_PRODUCTION_USER']->value) {?>
                            productionTab
                        <?php }?>
                        
                    ],
                    defaults: { collapsed: true },
                    bodyStyle:'border-right:0; background: rgb(237, 240, 252)'
                });

                centreRegion = new Ext.Panel({
                    region:'center',
                    cls:'empty',
                    layout: 'anchor',
                    id: 'centreRegionPanel',
                    bodyStyle: 'background:#fff url("<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/logo_transparent.png") 50% 50% no-repeat;',
                    listeners:
                    {
                        render: function(panel) 
                        {
                            centreRegion.body.dom.id = "app";
                            var shadow = document.createElement('div');
                            shadow.id = 'shadow';
                            shadow.style = 'display:flex;height:100%;';
                            centreRegion.body.dom.appendChild(shadow);
                        }
                    }
                });

                /* Viewport that contains all the layout */
                viewport = new Ext.Viewport({
                    layout:'border', 
                    id:'viewportObj',
                    items: [
                        { xtype: 'panel', region:'north', html: "<div id='header'><div><img src='<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/logo_transparent_thumbnail.png'>&#8203;</div><h1>Control Centre</h1><div id='logOutBtnHolder'></div></div>" },
                        accordion,
                        centreRegion
                    ]
                }); /* end of viewport */

                /* set onclick event handlers on sub menu links */
                var subMenus = document.getElementsByTagName('ul');
                var subMenuLinks = [];
                for (var i = 0, subMenu, subMenuLink; i < subMenus.length; i++)
                {
                    subMenu = subMenus[i];
                    if (subMenu.className == 'submenu')
                    {
                        subMenuLinks = subMenu.getElementsByTagName('a');
                        for (var j = 0; j < subMenuLinks.length; j++)
                        {
                            subMenuLink = subMenuLinks[j];
                            subMenuLink.onclick = function(){ itemClicked(this); return false; };
                        }
                    }
                }

                var logOutBtn = new Ext.Button({
                    renderTo: 'logOutBtnHolder',
                            text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLogOut');?>
",
                    handler: logOut
                });

    	/* if there is only one option then load it */

<?php if ($_smarty_tpl->tpl_vars['TPX_LOGIN_SYSTEM_ADMIN']->value) {?>
    
                itemClicked(homeTab);
    
<?php }
if (($_smarty_tpl->tpl_vars['TPX_LOGIN_STORE_USER']->value || $_smarty_tpl->tpl_vars['TPX_LOGIN_DISTRIBUTION_CENTRE_USER']->value)) {?>
    
                itemClicked(collectFromStoreTab);
    
<?php }
if ($_smarty_tpl->tpl_vars['TPX_LOGIN_SITE_ADMIN']->value) {?>
    
                itemClicked(usersTab);
    
<?php }
if ($_smarty_tpl->tpl_vars['TPX_LOGIN_BRAND_OWNER']->value) {?>
    
                itemClicked(dataExportTab);
    
<?php }
if ($_smarty_tpl->tpl_vars['TPX_LOGIN_PRODUCTION_USER']->value) {?>
    
                itemClicked(productionTab);
    
<?php }?>


            });

        //]]>
        <?php echo '</script'; ?>
>
    </head>
    <body>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/taopix.js'),$_smarty_tpl);?>
"><?php echo '</script'; ?>
>
    </body>
</html><?php }
}
