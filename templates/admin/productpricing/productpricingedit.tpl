{literal}
{/literal}{$localizedcodesjavascript}{literal}
{/literal}{$localizednamesjavascript}{literal}
{/literal}{$languagecodesjavascript}{literal}
{/literal}{$languagenamesjavascript}{literal}
{/literal}{$sitegrouplocalizedcodesjavascript}{literal}
{/literal}{$sitegrouplocalizednamesjavascript}{literal}


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
		Ext.taopix.loadJavascript(gComponentsAddDialogObj, '', 'index.php?fsaction=Admin.saveAsPriceList&ref={/literal}{$ref}{literal}', '', '', 'initializeSaveAsPriceList', false);
	}

	function addPricingSaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminProductPricing.add&ref={/literal}{$ref}{literal}';
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

		{/literal}{if $scbo == 1}{literal}
			if (Ext.getCmp('useexternalshoppingcart').checked)
			{
				paramArray['useexternalshoppingcart'] = '1';
			}
			else
			{
				paramArray['useexternalshoppingcart'] = '0';
			}
		{/literal}{/if}{literal}

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
		   	 	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoLicenseKeysSelected#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

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
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
    				break;
    			case 2:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
        			break;
    			case 3:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndError#}{literal}";
        			break;
    			case 4:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError2#}{literal}";
        			break;
    			case 5:
    				ERRORMSG = "{/literal}{#str_ErrorComponentPriceError#}{literal}";
        			break;
    			case 6:
    				ERRORMSG = "{/literal}{#str_ErrorProductPriceError#}{literal}";
        			break;
    			case 7:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndLimitError#}{literal}";
        			break;
	    	}

			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('componenttabpanel');
    		tabpanel.activate('priceTab');
    		return false;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", savePriceCallback);
		}
	}

	function editPricingSaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentpricinggrid'));

		var submitURL = 'index.php?fsaction=AdminProductPricing.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
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

		{/literal}{if $scbo == 1}{literal}
			if (Ext.getCmp('useexternalshoppingcart').checked)
			{
				paramArray['useexternalshoppingcart'] = '1';
			}
			else
			{
				paramArray['useexternalshoppingcart'] = '0';
			}
		{/literal}{/if}{literal}

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
	   	 		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoLicenseKeysSelected#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

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
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
    				break;
    			case 2:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
        			break;
    			case 3:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndError#}{literal}";
        			break;
    			case 4:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError2#}{literal}";
        			break;
    			case 5:
    				ERRORMSG = "{/literal}{#str_ErrorComponentPriceError#}{literal}";
        			break;
    			case 6:
    				ERRORMSG = "{/literal}{#str_ErrorProductPriceError#}{literal}";
        			break;
    			case 7:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndLimitError#}{literal}";
        			break;
	    	}

			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('componenttabpanel');
    		tabpanel.activate('priceTab');
    		return false;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", savePriceCallback);
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

	var str_LabelLanguageName = "{/literal}{#str_LabelLanguageName#}{literal}";
	var str_localizedNameLabel = "{/literal}{#str_LabelName#}{literal}";
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

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
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
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
		disabled: '{/literal}{$controldisabled}{literal}',
		{/literal}{if $companycode == ""}{literal}
			defvalue: 'GLOBAL',
		{/literal}{else}{literal}
			defvalue: '{/literal}{$companycode}{literal}',
		{/literal}{/if}{literal}
			options: {
			ref: '{/literal}{$ref}{literal}',
			storeId: 'companyStore',
			includeGlobal: '{/literal}{$includeglobal}{literal}',
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
		{/literal}
		{section name=index loop=$assignedLicenseKeys}
		{if $smarty.section.index.last}
			{$assignedLicenseKeys[index]}
		{else}
			{$assignedLicenseKeys[index]},
		{/if}
		{/section}
		{literal}
	];

	var gComponentsAddPricingDialogObj = new Ext.Window({
		id: 'componentAddPricingDialog',
		title: "{/literal}{$title}{literal}",
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
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
				ctCls: 'width_100',
				{/literal}{if $isactive == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			},
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ Ext.getCmp('componentAddPricingDialog').close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'priceAddEditButton',
				cls: 'x-btn-right',
				{/literal}{if $id == 0}{literal}
					handler: addPricingSaveHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: editPricingSaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}

			}
		]
	});

	var priceinfo = '{/literal}{$priceinfo}{literal}';

	var newPricingTaopixPanel =
    {
        id: 'componentPricingPanel',
        name:'componentPricingPanel',
        xtype:'taopixPricingPanel',
        ref: '{/literal}{$ref}{literal}',
        windowToMask: gComponentsAddPricingDialogObj,
        isProduct: '1',
        useExternalShoppingCart: '{/literal}{$scbo}{literal}',
        useExternalShoppingCartChecked: '{/literal}{$externalcartchecked}{literal}',
        company: '{/literal}{$companycode}{literal}',
        category: 'PRODUCT',
        pricingDecimalPlaces: 4,
        pricing:{
            pricingModel: '3',
            price:'{/literal}{$price}{literal}',
            isPriceList:'{/literal}{$ispricelist}{literal}',
            priceListID: '{/literal}{$pricelistid}{literal}',
            qtyIsDropDown: '{/literal}{$quantityisdropdown}{literal}',
            taxCode: '{/literal}{$taxcode}{literal}',
            productType: '{/literal}{$producttype}{literal}'
        },
        licenseKeyStoreURL: 'index.php?fsaction=AdminProductPricing.getLicenseKeyFromCompany&ref={/literal}{$ref}{literal}&productcode={/literal}{$productcode}{literal}&id={/literal}{$parentid}{literal}&companycode={/literal}{$companycode}{literal}',
        LicenseKeys:{
            assignedLicenseKeys: assignedKeys,
            defaultChecked: '{/literal}{$defaultChecked}{literal}'
        },
        {/literal}{if $id > 0}{literal}
        	additionalInfo: {langList: langListStore, dataList: priceinfo},
        {/literal}{else}{literal}
        	additionalInfo: {langList: langListStore, dataList: []},
        {/literal}{/if}{literal}
        priceDescription: {
            langList: priceDescriptionList,
            dataList: dataList2
        },
        images: {
            deleteImg: '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png',
            addimg: '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png'
        },
        errorTitle: "{/literal}{#str_TitleError#}{literal}",
        errorMessage1: "{/literal}{#str_ErrorRangeStartError1#}{literal}",
    	errorMessage2: "{/literal}{#str_ErrorRangeStartError2#}{literal}",
    	errorMessage3: "{/literal}{#str_ErrorRangeEndError#}{literal}",
    	errorMessage4: "{/literal}{#str_ErrorComponentPriceError#}{literal}",
    	errorMessage5: "{/literal}{#str_ErrorProductPriceError#}{literal}",
    	errorMessage7: "{/literal}{#str_ErrorRangeEndLimitError#}{literal}",
    	errorMessage6: "{/literal}{#str_ErrorEnterValidPricing#}{literal}",
		errorMessage6Title: "{/literal}{#str_ErrorTitleInvalidPricing#}{literal}",
    	defaultLanguage: '{/literal}{$defaultlanguagecode}{literal}'
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
		    {/literal}{if $optionms}{literal}
		    	{/literal}{if !$companyLogin}{literal}
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
				{/literal}{/if}{literal}
			{/literal}{/if}{literal}
				newPricingTaopixPanel,
				{ xtype: 'hidden', id: 'productcode', name: 'productcode', value: '{/literal}{$productcode}{literal}',  post: true},
				{ xtype: 'hidden', id: 'categorycode', name: 'categorycode', value: '{/literal}{$categorycode}{literal}',  post: true},
				{ xtype: 'hidden', id: 'inispricelist', name: 'inispricelist', value:'{/literal}{$ispricelist}{literal}',  post: true},
				{ xtype: 'hidden', id: 'inpricelistid', name: 'inpricelistid', value: '{/literal}{$pricelistid}{literal}',  post: true},
				{ xtype: 'hidden', id: 'inpricelinkid', name: 'inpricelinkid', value: '{/literal}{$pricelinkid}{literal}',  post: true}
			]
	});
	gComponentsAddPricingDialogObj.add(dialogPricingFormPanelObj);
	gComponentsAddPricingDialogObj.show();
}
{/literal}