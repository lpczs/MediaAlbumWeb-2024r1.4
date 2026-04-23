<?php
/* Smarty version 4.5.3, created on 2026-03-07 03:43:23
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\productpricing\productpricingedit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ab9edb7ea495_09351076',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '79dd053d97ebeff579abd754b2fc01e7ea4242ee' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\productpricing\\productpricingedit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ab9edb7ea495_09351076 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['localizedcodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['localizednamesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['languagecodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['languagenamesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['sitegrouplocalizedcodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['sitegrouplocalizednamesjavascript']->value;?>



var getLocalisedData = function(populateLangs, gLocalizedNamesArray, gLocalizedCodesArray)
{
	var langListStore = [], dataList = [];
	populateLangs = 1;

	for (var i =0; i < gAllLanguageCodesArray.length; i++)
	{
		var languageName = "";
		var languageCode = "";
		var languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, gLocalizedCodesArray[i]);
		if (languageNameIndex > -1)
		{
			languageName = gAllLanguageNamesArray[languageNameIndex];
			languageCode = gAllLanguageCodesArray[languageNameIndex];
		}
		if ((languageName) && (languageName!=undefined)) dataList.push([languageCode,languageName,gLocalizedNamesArray[i]]);

		if (populateLangs == 1)
		{
			if (ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]) == -1)
			{
				langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
			}
		}
	}
	return {'langListStore': langListStore, 'dataList': dataList};
};

function initialize(pParams)
{
	function onSaveAsPriceList(btn, ev)
	{
		Ext.taopix.loadJavascript(gComponentsAddDialogObj, '', 'index.php?fsaction=Admin.saveAsPriceList&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', '', '', 'initializeSaveAsPriceList', false);
	}

	function addPricingSaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminProductPricing.add&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
';
		var fp = Ext.getCmp('productPricingForm'), form = fp.getForm();
		var submit = true;

		var paramArray = new Object();
		paramArray['isactive'] = '';

		if (Ext.getCmp('isPriceActive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		if (Ext.getCmp('fixedquantityrange').checked)
		{
			paramArray['quantitytypeisdropdown'] = '1';
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}

		<?php if ($_smarty_tpl->tpl_vars['scbo']->value == 1) {?>
			if (Ext.getCmp('useexternalshoppingcart').checked)
			{
				paramArray['useexternalshoppingcart'] = '1';
			}
			else
			{
				paramArray['useexternalshoppingcart'] = '0';
			}
		<?php }?>

		if (Ext.getCmp('defaultLicenseKeys').checked)
		{
			paramArray['groupcodes'] = '';
		}
		else
		{
			paramArray['groupcodes'] = Ext.getCmp('componentPricingPanel').covertLicenseKeySelectionToString();
		}

		var pricingModel = 3;

		paramArray['pricingmodel'] = pricingModel;

		if (!Ext.getCmp('defaultLicenseKeys').checked)
		{
			if (! Ext.getCmp('licenseKeyGrid').selModel.getCount() >= 1)
		    {
		   	 	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoLicenseKeysSelected');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

				submit = false;
	   		 	var tabpanel = Ext.getCmp('componenttabpanel');
	   		 	tabpanel.activate('licenseKeyTab');
	    		return false;
	    	}
		}

		if (Ext.getCmp('price').isValid() != 0)
		{
    		switch (Ext.getCmp('price').isValid())
    		{
    			case 1:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
    				break;
    			case 2:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
        			break;
    			case 3:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndError');?>
";
        			break;
    			case 4:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError2');?>
";
        			break;
    			case 5:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorComponentPriceError');?>
";
        			break;
    			case 6:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProductPriceError');?>
";
        			break;
    			case 7:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndLimitError');?>
";
        			break;
	    	}

			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('componenttabpanel');
    		tabpanel.activate('priceTab');
    		return false;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", savePriceCallback);
		}
	}

	function editPricingSaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentpricinggrid'));

		var submitURL = 'index.php?fsaction=AdminProductPricing.edit&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id=' + selectID;
		var fp = Ext.getCmp('productPricingForm'), form = fp.getForm();
		var submit = true;

		var paramArray = new Object();
		paramArray['isactive'] = '';

		var pricingModel = 3;

		paramArray['pricingmodel'] = pricingModel;

		if (Ext.getCmp('isPriceActive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		if (Ext.getCmp('fixedquantityrange').checked)
		{
			paramArray['quantitytypeisdropdown'] = '1';
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}

		<?php if ($_smarty_tpl->tpl_vars['scbo']->value == 1) {?>
			if (Ext.getCmp('useexternalshoppingcart').checked)
			{
				paramArray['useexternalshoppingcart'] = '1';
			}
			else
			{
				paramArray['useexternalshoppingcart'] = '0';
			}
		<?php }?>

		if (Ext.getCmp('defaultLicenseKeys').checked)
		{
			paramArray['groupcodes'] = '';
		}
		else
		{
			paramArray['groupcodes'] = Ext.getCmp('componentPricingPanel').covertLicenseKeySelectionToString();
		}

		if (!Ext.getCmp('defaultLicenseKeys').checked)
		{
			if (! Ext.getCmp('licenseKeyGrid').selModel.getCount() >= 1)
	   	 	{
	   	 		Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoLicenseKeysSelected');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

				submit = false;
	   		 	var tabpanel = Ext.getCmp('componenttabpanel');
	   	 		tabpanel.activate('licenseKeyTab');
	    		return false;
	    	}
		}

		if (Ext.getCmp('price').isValid() != 0)
		{
			switch (Ext.getCmp('price').isValid())
    		{
    			case 1:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
    				break;
    			case 2:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
        			break;
    			case 3:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndError');?>
";
        			break;
    			case 4:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError2');?>
";
        			break;
    			case 5:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorComponentPriceError');?>
";
        			break;
    			case 6:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProductPriceError');?>
";
        			break;
    			case 7:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndLimitError');?>
";
        			break;
	    	}

			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('componenttabpanel');
    		tabpanel.activate('priceTab');
    		return false;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", savePriceCallback);
		}
	}

	function savePriceCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = Ext.getCmp('pricingGrid');
			var productGrid = Ext.getCmp('productgrid');
			gridObj.store.reload();
			productGrid.store.reload();
			gComponentsAddPricingDialogObj.close();
		}
		else
		{
			icon = Ext.MessageBox.WARNING;

			Ext.MessageBox.show({
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
	}

	var str_LabelLanguageName = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
";
	var str_localizedNameLabel = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
";
	var deleteImg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png';
	var addimg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png';

    /*product price description */
    var priceDescriptionList = [];
	var dataList2 = [ ];
	for (var i =0; i < gAllLanguageCodesArray.length; i++)
	{
		var groupLabelLanguageName = "";
		var groupLabelLanguageCode = "";
		var groupLabeLlanguageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, gSiteGroupLocalizedCodesArray[i]);
		if (groupLabeLlanguageNameIndex > -1)
		{
			groupLabelLanguageName = gAllLanguageNamesArray[groupLabeLlanguageNameIndex];
			groupLabelLanguageCode = gAllLanguageCodesArray[groupLabeLlanguageNameIndex];
		}
    	if ((groupLabelLanguageName) && (groupLabelLanguageName!=undefined)) dataList2.push([groupLabelLanguageCode,groupLabelLanguageName,gSiteGroupLocalizedNamesArray[i]]);

    	var groupLabellanguageCodeIndex = ArrayIndexOf(gSiteGroupLocalizedCodesArray, gAllLanguageCodesArray[i]);
    	if (groupLabellanguageCodeIndex == -1)
    	{
    		priceDescriptionList.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
    	}
   };

   	/* price additional info */
	var localisedData = getLocalisedData(1, gLocalizedNamesArray, gLocalizedCodesArray);
	var langListStore = localisedData.langListStore;
	var dataList = localisedData.dataList;

   	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:30,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
",
		hideLabel:false,
		listeners:{
			select:{
				fn: function(comboBox, record, index){

			Ext.getCmp('licenseKeyGrid').store.reload({
						params: { companycode: comboBox.getValue() }
						});
					}
				}
		},
		allowBlank:false,
		disabled: '<?php echo $_smarty_tpl->tpl_vars['controldisabled']->value;?>
',
		<?php if ($_smarty_tpl->tpl_vars['companycode']->value == '') {?>
			defvalue: 'GLOBAL',
		<?php } else { ?>
			defvalue: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
		<?php }?>
			options: {
			ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
			storeId: 'companyStore',
			includeGlobal: '<?php echo $_smarty_tpl->tpl_vars['includeglobal']->value;?>
',
			includeShowAll: '0',
			onchange: function(){
                var companyCode = companyCombo.getValue();
                if (companyCode == 'GLOBAL'){
                    companyCode = '';
                }
            }
		}
	});

	var assignedKeys = [
		
		<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['assignedLicenseKeys']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
		<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
			<?php echo $_smarty_tpl->tpl_vars['assignedLicenseKeys']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)];?>

		<?php } else { ?>
			<?php echo $_smarty_tpl->tpl_vars['assignedLicenseKeys']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)];?>
,
		<?php }?>
		<?php
}
}
?>
		
	];

	var gComponentsAddPricingDialogObj = new Ext.Window({
		id: 'componentAddPricingDialog',
		title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
	  	closable: false,
	  	plain:true,
	 	modal:true,
	  	autoHeight:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	height: 'auto',
	  	width: 700,
	 	cls: 'left-right-buttons',
	 	listeners: {
			'close': {
				fn: function(){
                    productPricingEditWindowExists = false;
				}
			}
		},
	  	buttons:
		[
			{
				xtype: 'checkbox',
				id: 'isPriceActive',
				name: 'isPriceActive',
				boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
",
				post: true,
				cls: 'x-btn-left',
				ctCls: 'width_100',
				<?php if ($_smarty_tpl->tpl_vars['isactive']->value == 1) {?>
					checked: true
				<?php } else { ?>
					checked: false
				<?php }?>
			},
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
				handler: function(){ Ext.getCmp('componentAddPricingDialog').close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'priceAddEditButton',
				cls: 'x-btn-right',
				<?php if ($_smarty_tpl->tpl_vars['id']->value == 0) {?>
					handler: addPricingSaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
"
				<?php } else { ?>
					handler: editPricingSaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
"
				<?php }?>

			}
		]
	});

	var priceinfo = '<?php echo $_smarty_tpl->tpl_vars['priceinfo']->value;?>
';

	var newPricingTaopixPanel =
    {
        id: 'componentPricingPanel',
        name:'componentPricingPanel',
        xtype:'taopixPricingPanel',
        ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
        windowToMask: gComponentsAddPricingDialogObj,
        isProduct: '1',
        useExternalShoppingCart: '<?php echo $_smarty_tpl->tpl_vars['scbo']->value;?>
',
        useExternalShoppingCartChecked: '<?php echo $_smarty_tpl->tpl_vars['externalcartchecked']->value;?>
',
        company: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
        category: 'PRODUCT',
        pricingDecimalPlaces: 4,
        pricing:{
            pricingModel: '3',
            price:'<?php echo $_smarty_tpl->tpl_vars['price']->value;?>
',
            isPriceList:'<?php echo $_smarty_tpl->tpl_vars['ispricelist']->value;?>
',
            priceListID: '<?php echo $_smarty_tpl->tpl_vars['pricelistid']->value;?>
',
            qtyIsDropDown: '<?php echo $_smarty_tpl->tpl_vars['quantityisdropdown']->value;?>
',
            taxCode: '<?php echo $_smarty_tpl->tpl_vars['taxcode']->value;?>
',
            productType: '<?php echo $_smarty_tpl->tpl_vars['producttype']->value;?>
'
        },
        licenseKeyStoreURL: 'index.php?fsaction=AdminProductPricing.getLicenseKeyFromCompany&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&productcode=<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
&id=<?php echo $_smarty_tpl->tpl_vars['parentid']->value;?>
&companycode=<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
        LicenseKeys:{
            assignedLicenseKeys: assignedKeys,
            defaultChecked: '<?php echo $_smarty_tpl->tpl_vars['defaultChecked']->value;?>
'
        },
        <?php if ($_smarty_tpl->tpl_vars['id']->value > 0) {?>
        	additionalInfo: {langList: langListStore, dataList: priceinfo},
        <?php } else { ?>
        	additionalInfo: {langList: langListStore, dataList: []},
        <?php }?>
        priceDescription: {
            langList: priceDescriptionList,
            dataList: dataList2
        },
        images: {
            deleteImg: '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png',
            addimg: '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png'
        },
        errorTitle: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
",
        errorMessage1: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
",
    	errorMessage2: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError2');?>
",
    	errorMessage3: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndError');?>
",
    	errorMessage4: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorComponentPriceError');?>
",
    	errorMessage5: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProductPriceError');?>
",
    	errorMessage7: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndLimitError');?>
",
    	errorMessage6: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorEnterValidPricing');?>
",
		errorMessage6Title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorTitleInvalidPricing');?>
",
    	defaultLanguage: '<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
'
    };

	var dialogPricingFormPanelObj = new Ext.taopix.FormPanel({
		id: 'productPricingForm',
		header: false,
		frame:true,
		autoWidth: true,
		autoHeight:true,
		layout: 'form',
		defaultType: 'textfield',
		bodyBorder: false,
		border: false,
		defaults: {labelWidth:110},
		items: [
		    <?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
		    	<?php if (!$_smarty_tpl->tpl_vars['companyLogin']->value) {?>
		    {
            xtype: 'panel',
            id: 'topPanel',
            layout: 'column',
            style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf; margin-bottom:7px;',
            columns: 2,
            plain:true,
            bodyBorder: false,
			border: false,
            defaults:{
                labelWidth: 70,
                autoScroll: true
            },
            bodyStyle:'padding:5px 5px 0; border-top: 0px',
			items: [
                new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 225}, width:350,
                    items:	companyCombo
                })
			]},
				<?php }?>
			<?php }?>
				newPricingTaopixPanel,
				{ xtype: 'hidden', id: 'productcode', name: 'productcode', value: '<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
',  post: true},
				{ xtype: 'hidden', id: 'categorycode', name: 'categorycode', value: '<?php echo $_smarty_tpl->tpl_vars['categorycode']->value;?>
',  post: true},
				{ xtype: 'hidden', id: 'inispricelist', name: 'inispricelist', value:'<?php echo $_smarty_tpl->tpl_vars['ispricelist']->value;?>
',  post: true},
				{ xtype: 'hidden', id: 'inpricelistid', name: 'inpricelistid', value: '<?php echo $_smarty_tpl->tpl_vars['pricelistid']->value;?>
',  post: true},
				{ xtype: 'hidden', id: 'inpricelinkid', name: 'inpricelinkid', value: '<?php echo $_smarty_tpl->tpl_vars['pricelinkid']->value;?>
',  post: true}
			]
	});
	gComponentsAddPricingDialogObj.add(dialogPricingFormPanelObj);
	gComponentsAddPricingDialogObj.show();
}
<?php }
}
