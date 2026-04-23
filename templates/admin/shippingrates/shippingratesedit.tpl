{literal}
function initialize(pParams)
{
	{/literal}{$localizedcodesjavascript}{literal}
	{/literal}{$localizednamesjavascript}{literal}
	{/literal}{$languagecodesjavascript}{literal}
	{/literal}{$languagenamesjavascript}{literal}

	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminShippingRates.add&ref={/literal}{$ref}{literal}&id=0';
		var fp = Ext.getCmp('mainform'), form = fp.getForm();
		var submit = true;
		var paramArray = new Object();
		paramArray['isactive'] = '';
		paramArray['ordervalueincludesdiscount'] = '';
		paramArray['taxcode'] = Ext.getCmp('taxcode').getValue();

		var shippingMethodCount =  Ext.getCmp('shippingmethodcode').store.getCount();

		if (shippingMethodCount == 0)
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoShippingMethod#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('maintabpanel');
    		tabpanel.activate('settingsTab');
    		return false;
		}

		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		if (Ext.getCmp('ordervalueincludesdiscount').checked)
		{
			paramArray['ordervalueincludesdiscount'] = '1';
		}
		else
		{
			paramArray['ordervalueincludesdiscount'] = '0';
		}

		var licenseKeyGridObj = gDialogObj.findById('licenseKeyGrid');
		var selRecords = licenseKeyGridObj.selModel.getSelections();
		var codeList = '';

		for (var rec = 0; rec < selRecords.length; rec++)
		{
			codeList = codeList + selRecords[rec].data.code;
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}
		}

		paramArray['groupcode'] = codeList;

		{/literal}{if $optioncfs}{literal}
		var siteGroupGridObj = gDialogObj.findById('siteGroupListGrid');
		var selRecords = siteGroupGridObj.selModel.getSelections();
		var siteGroupList = '';

		for (var rec = 0; rec < selRecords.length; rec++)
		{
			siteGroupList = siteGroupList + selRecords[rec].data.code;
			if (rec != selRecords.length - 1)
			{
				siteGroupList = siteGroupList + ',';
			}
		}
		paramArray['sitegroup'] = siteGroupList;
		{/literal}{/if}{literal}

		var orderMinValue = "0.00";
   	 	var orderMaxValue = "0.00";
   	 	var orderMinValueNumber = 0.00;
    	var orderMaxValueNumber = 0.00;
    	var orderValueRange = Ext.getCmp('ordervaluerange').getValue();

    	if(orderValueRange != '')
		{
    		var orderMinValue = Ext.getCmp('orderminvalue').getValue();
			var orderMaxValue = Ext.getCmp('ordermaxvalue').getValue();
		 	if (orderMinValue != "")
		 	{
		   		orderMinValueNumber = parseFloat(orderMinValue);
		 	}

		 	if (orderMaxValue != "")
		 	{
		    	orderMaxValueNumber = parseFloat(orderMaxValue);
		 	}

		 	if (orderMaxValueNumber <= 0.00)
		 	{
			 	Ext.getCmp('ordermaxvalue').markInvalid("{/literal}{#str_ErrorOrderValueRangeError1#}{literal}");
				submit = false;
		 	}

		 	if (orderMaxValueNumber <= orderMinValueNumber)
		 	{
				Ext.getCmp('ordermaxvalue').markInvalid("{/literal}{#str_ErrorOrderValueRangeError2#}{literal}");
		     	submit = false;
		 	}
		}

    	if (!licenseKeysCheckboxSelectionModelObj.getCount() >= 1)
    	{
    		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoLicenseKeysSelected#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			submit = false;
    		var tabpanel = Ext.getCmp('maintabpanel');
    		tabpanel.activate('licenseTab');
    		return false;
    	}

    	if (Ext.getCmp('shippingrates').isValid() != 0)
		{
    		switch (Ext.getCmp('shippingrates').isValid())
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
	    	}

			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('maintabpanel');
    		tabpanel.activate('shipratestab');
    		return false;
		}

    	if (submit)
   	 	{
    		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
    	}
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		var submit = true;
		var submitURL = 'index.php?fsaction=AdminShippingRates.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('mainform'), form = fp.getForm();

		var paramArray = new Object();
		paramArray['isactive'] = '';
		paramArray['taxcode'] = Ext.getCmp('taxcode').getValue();

		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		if (Ext.getCmp('ordervalueincludesdiscount').checked)
		{
			paramArray['ordervalueincludesdiscount'] = '1';
		}
		else
		{
			paramArray['ordervalueincludesdiscount'] = '0';
		}

		var licenseKeyGridObj = gDialogObj.findById('licenseKeyGrid');
		var selRecords = licenseKeyGridObj.selModel.getSelections();
		var codeList = '';

		for (var rec = 0; rec < selRecords.length; rec++)
		{
			codeList = codeList + selRecords[rec].data.code;
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}
		}
		paramArray['groupcode'] = codeList;

		{/literal}{if $optioncfs}{literal}
		var siteGroupGridObj = gDialogObj.findById('siteGroupListGrid');
		var selRecords = siteGroupGridObj.selModel.getSelections();
		var siteGroupList = '';

		for (var rec = 0; rec < selRecords.length; rec++)
		{
			siteGroupList = siteGroupList + selRecords[rec].data.code;

			if (rec != selRecords.length - 1)
			{
				siteGroupList = siteGroupList + ',';
			}
		}
		paramArray['sitegroup'] = siteGroupList;
		{/literal}{/if}{literal}

		var orderMinValue = "0.00";
    	var orderMaxValue = "0.00";
    	var orderMinValueNumber = 0.00;
    	var orderMaxValueNumber = 0.00;
    	var orderValueRange = Ext.getCmp('ordervaluerange').getValue();

    	if(orderValueRange != '')
		{
    		var orderMinValue = Ext.getCmp('orderminvalue').getValue();
			var orderMaxValue = Ext.getCmp('ordermaxvalue').getValue();

		 	if (orderMinValue != "")
		 	{
		   		orderMinValueNumber = parseFloat(orderMinValue);
		 	}

		 	if (orderMaxValue != "")
		 	{
		   		orderMaxValueNumber = parseFloat(orderMaxValue);
		 	}

		 	if (orderMaxValueNumber <= 0.00)
		 	{
				Ext.getCmp('ordermaxvalue').markInvalid("{/literal}{#str_ErrorOrderValueRangeError1#}{literal}");
			 	submit = false;
		 	}

		 	if (orderMaxValueNumber <= orderMinValueNumber)
		 	{
				 Ext.getCmp('ordermaxvalue').markInvalid("{/literal}{#str_ErrorOrderValueRangeError2#}{literal}");
		    	 submit = false;
		 	}
		}

    	if (!licenseKeysCheckboxSelectionModelObj.getCount() >= 1)
    	{
    		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoLicenseKeysSelected#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			submit = false;
    		var tabpanel = Ext.getCmp('maintabpanel');
    		tabpanel.activate('licenseTab');
    		return false;
    	}

    	if (Ext.getCmp('shippingrates').isValid() != 0)
		{
    		switch (Ext.getCmp('shippingrates').isValid())
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
    		}
    		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('maintabpanel');
    		tabpanel.activate('shipratestab');
    		return false;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
		}
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			gridObj.store.reload();
			Ext.getCmp('dialog').close();
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

	function rangeConfig(comboBox, record, index)
	{
		if (index == 0)
		{
			 Ext.getCmp('orderminvalue').disable();
			 Ext.getCmp('ordermaxvalue').disable();
			 Ext.getCmp('ordervalueincludesdiscount').disable();
			 Ext.getCmp('orderminvalue').setValue(0);
			 Ext.getCmp('ordermaxvalue').setValue(0);
			 Ext.getCmp('ordervalueincludesdiscount').setValue(0);
		}
		else
		{
			Ext.getCmp('orderminvalue').enable();
			Ext.getCmp('ordermaxvalue').enable();
			Ext.getCmp('ordervalueincludesdiscount').enable();
		}
	}

	function defaultRenderer(value, p, record)
	{
		if (value == '')
		{
			return "<i>{/literal}{#str_LabelDefault#}{literal}</i>";
		}
		else
		{
			return value;
		}
	}

	function licenseActiveRenderer(value, p, record)
	{
		if (record.data.code == '')
		{
			return '';
		}
		else
		{
			if (value == 0)
			{
				return "{/literal}{#str_LabelInactive#}{literal}";
			}
			else
			{
				return "{/literal}{#str_LabelActive#}{literal}";
			}
		}
	}

	function setDefault()
	{
		switch (this.storeId)
		{
			case 'zoneStore':
				var shippingZoneVal = Ext.getCmp('shippingzonecode').store.getAt(0);
				Ext.getCmp('shippingzonecode').setValue(shippingZoneVal.data['id']);
			break;
			case 'productStore':
				var productVal = Ext.getCmp('productcode').store.getAt(0);
				Ext.getCmp('productcode').setValue(productVal.data['id']);
			break;
		}
	}

	function validate(value, allowDecimal, allowNegative)
	{
		if (! isNumeric(value, true, false))
		{
			valid = false;
		}
		else
		{
			valid = true;
		}
		return valid;
	}

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('code').getValue();
   	    code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");
   	    Ext.getCmp('code').setValue(code);
	}

	Ext.layout.FormLayout.prototype.trackLabels = true;

	licenseKeysCheckboxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(licenseKeysCheckboxSelectionModelObj)
			{
				var selectionCount = licenseKeysCheckboxSelectionModelObj.getCount();
			}
		}
	});

	var str_LabelLanguageName = "{/literal}{#str_LabelLanguageName#}{literal}";
	var str_localizedInfoLabel = "{/literal}{#str_LabelInformation#}{literal}";

	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	var langListStore = [];
	var dataList = [];
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

    	var languageCodeIndex = ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]);
    	if (languageCodeIndex == -1)
    	{
    		langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
    	}
   }

	var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel',
		name:'info',
		height: 281,
		width: 772,
		post: true,
		style: 'border:1px solid #b4b8c8',
		data: {langList: langListStore, dataList: dataList},
		settings:
		{
			headers: {langLabel: str_LabelLanguageName,  textLabel: str_localizedInfoLabel, deletePic: deleteImg, addPic: addimg, startMinValue:0},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 215,   textCol: 500, delCol: 35},
			fieldWidth: {langField: 195, textField: 490},
			errorMsg: {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});

	/*Shipping Rate Weight Lines */

	var columnWidth = [ 145, 145, 146, 146];
	var dataList = {/literal}{$shippingrates}{literal};
	var columnList = [
		{ title: "{/literal}{#str_WeightPriceRangeStart#}{literal}" },
		{ title: "{/literal}{#str_WeightPriceRangeEnd#}{literal}" },
		{ title: "{/literal}{#str_LabelShippingCost#}{literal}" },
		{ title: "{/literal}{#str_LabelShippingSell#}{literal}" },
		{ title: "{/literal}{#str_LabelOrderValuePercent#}{literal}"}
	];

	var fieldsList = [
	{
	   fieldType: 'number',
	   range: 'qtyRange',
	   rangeStart: true,
	   emptyText: '',
	   allowBlank: false,
	   width: 125,
	   hideLabel: true, fieldLabel: '',
	   minValue: Number.NEGATIVE_INFINITY, maxValue: Number.POSITIVE_INFINITY,
	   allowDecimals: true, decimalPrecision: 4, decimalSeparator: '.', allowNegative: false, trailingDecimals: 4
   },
	{
	   fieldType: 'number',
	   range: 'qtyRange',
	   rangeEnd: true,
	   emptyText: '',
	   allowBlank: false,
	   width: 125,
	   hideLabel: true, fieldLabel: '',
	   minValue: Number.NEGATIVE_INFINITY,    maxValue: Number.POSITIVE_INFINITY,
	   allowDecimals: true, decimalPrecision: 4, decimalSeparator: '.', allowNegative: false ,trailingDecimals: 4
	},
	{
	   fieldType: 'number',
	   range: false,
	   emptyText: '',
	   allowBlank: false,
	   width: 125,
	   hideLabel: true, fieldLabel: '',
	   minValue: Number.NEGATIVE_INFINITY,    maxValue: Number.POSITIVE_INFINITY,
	   allowDecimals: true, decimalPrecision: 4, decimalSeparator: '.', allowNegative: true, trailingDecimals: 4
	},
	{
	   fieldType: 'number', range: false,
	   emptyText: '',
	   allowBlank: false,
	   width: 125,
	   hideLabel: true, fieldLabel: '',
	   minValue: Number.NEGATIVE_INFINITY,    maxValue: Number.POSITIVE_INFINITY,
	   allowDecimals: true, decimalPrecision: 4, decimalSeparator: '.', allowNegative: false, trailingDecimals: 4
	}
	,
	{
	   fieldType: 'number',
	   range: false,
	   emptyText: '',
	   allowBlank: false,
	   width: 125,
	   hideLabel: true, fieldLabel: '',
	   minValue: 0,    maxValue: 100,
	   allowDecimals: true, decimalPrecision: 2, decimalSeparator: '.', allowNegative: false, trailingDecimals: 2
	}];

	var ShippingWeightInputPanel = new Ext.taopix.InputOldFormatPanel({
	    id: 'shippingrates',
	    height: 255,
	    post: true,
	    width: 780,
	    name:'shippingrates',
	    data: dataList,
	    config: {
            outputFormat: 'OLDPRICES',
            fieldList: fieldsList,
            columnList: columnList,
            columnWidth: columnWidth,
            addPic: addimg,
            delPic: deleteImg,
            startMinValue:0
        }
	});

	var taxCodeStore =  new Ext.data.Store({
	id: 'taxcodestore',
	proxy: new Ext.data.HttpProxy({
		url:'index.php?fsaction=AjaxAPI.callback&ref={/literal}{$ref}{literal}&cmd=GETTAXCODELIST'
	}),
	reader: new Ext.data.ArrayReader({
		idIndex: 0
	},
	Ext.data.Record.create([
		{name: 'id', mapping: 0},
		{name: 'code', mapping: 1},
		{name: 'name', mapping: 2}
	])
	)
	});

	var taxCodeCombo =  new Ext.form.ComboBox({
		id: 'taxcode',
		name: 'taxcode',
		width:300,
		fieldLabel: gLabelTaxRate,
		mode: 'local',
		editable: false,
		forceSelection: true,
		store: taxCodeStore,
		selectOnFocus: true,
		triggerAction: 'all',
		valueField: 'code',
		displayField: 'name',
		useID: true,
		allowBlank: false,
		post: true
	});

	var shippingMethodsCombo = new Ext.form.ComboBox({
		id: 'shippingmethodcode',
		name: 'shippingmethodcode',
		mode: 'local',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank:false,
		triggerAction: 'all',
		width:380,
		fieldLabel: "{/literal}{#str_LabelShippingMethod#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: ['id', 'name', 'collectfromstore'],
			data: [
					{/literal}
					{section name=index loop=$shippingmethodslist}
					{if $smarty.section.index.last}

							["{$shippingmethodslist[index].code}", "{$shippingmethodslist[index].name}", "{$shippingmethodslist[index].collectfromstore}"]
					{else}
							["{$shippingmethodslist[index].code}", "{$shippingmethodslist[index].name}", "{$shippingmethodslist[index].collectfromstore}"],
					{/if}
					{/section}
					{literal}
				]
		}),
		valueField: 'id',
		listeners:{
			select:{
				fn: function(comboBox, record, index){
					{/literal}{if $optioncfs}{literal}
							if (record.data['collectfromstore'] == 1)
							{
								payInStoreOption.show();
								Ext.getCmp("collectfromstoretab").enable();

							}else
							{
								payInStoreOption.hide();
								Ext.getCmp("collectfromstoretab").disable();
							}
					{/literal}{/if}{literal}
					}
				}
		},
		displayField: 'name',
		{/literal}{if $isEdit == 1}{literal}
			value: "{/literal}{$shippingmethod}{literal}",
		{/literal}{/if}{literal}
		useID: true,
		post: true
	});

	{/literal}{if $optioncfs}{literal}
		var payInStoreOption = new Ext.form.ComboBox({
			id: 'payinstoreoption',
			name: 'payinstoreoption',
			width:250,
			mode: 'local',
			editable: false,
			forceSelection: true,
			selectOnFocus: true,
			listeners:{
				select:{
					fn: rangeConfig
				}
			},
			triggerAction: 'all',
			fieldLabel: "{/literal}{#str_LabelPayInStore#}{literal}",
			store: new Ext.data.ArrayStore({
				id: 0,
				fields: ['id', 'name'],
				data: [
						[0, "{/literal}{#str_LabelPayInStoreAllowed#}{literal}"],
						[1, "{/literal}{#str_LabelPayInStoreNotAllowed#}{literal}"],
						[2, "{/literal}{#str_LabelPayInStoreOnly#}{literal}"]
					]
			}),
			valueField: 'id',
			displayField: 'name',
			{/literal}{if $isEdit == 1}{literal}
				value: '{/literal}{$payinstoreallowed}{literal}',
			{/literal}{/if}{literal}
			useID: true,
			post: true
		});
	{/literal}{/if}{literal}

	var shippingZoneStore = new Ext.data.Store({
		id: 'zoneStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminShippingRates.getShippingZonesFromCompany&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'name', mapping: 1}
			])
		),
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	var productsStore = new Ext.data.Store({
		id: 'productStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminShippingRates.getProductsFromCompany&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'name', mapping: 1}
			])
		),
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	var shippingZonesCombo = new Ext.form.ComboBox({
		id: 'shippingzonecode',
		name: 'shippingzonecode',
		mode: 'local',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelShippingZone#}{literal}",
		store: shippingZoneStore,
		valueField: 'id',
		displayField: 'name',
		width:380,
		useID: true,
		post: true
	});

	var productCombo = new Ext.form.ComboBox({
		id: 'productcode',
		name: 'productcode',
		mode: 'local',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank:false,
		triggerAction: 'all',
		width:380,
		fieldLabel: "{/literal}{#str_LabelProduct#}{literal}",
		store: productsStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		post: true
	});

	var LicenseKeyStore = new Ext.data.Store({
		id: 'lKeyStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminShippingRates.getLicenseKeyFromCompany&ref={/literal}{$ref}{literal}&ratecode={/literal}{$shippingratecode}{literal}'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'active', mapping: 2}
			])
		),
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	var licenseKeysGrid =  new Ext.grid.GridPanel({
		id: 'licenseKeyGrid',
		style:'border:1px solid #B5B8C8',
		width: 772,
		height: 281,
		deferRowRender:false,
		store: LicenseKeyStore,
		sm: licenseKeysCheckboxSelectionModelObj,
		ctCls: 'grid',
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				sortable: true,
				resizable: true
			},
			columns: [
				licenseKeysCheckboxSelectionModelObj,
				{header: 'ID', width: 30, dataIndex: 'id', sortable: true, hidden: true},
				{header: "{/literal}{#str_LabelCode#}{literal}", width: 643, renderer: defaultRenderer, dataIndex: 'code'},
				{header: "{/literal}{#str_LabelStatus#}{literal}", width: 97, dataIndex: 'active', renderer: licenseActiveRenderer, align: 'right'}
			]
		}),
		enableColLock:true,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true
	});

	var siteGroupListCheckboxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({checkOnly: true});

	var siteGroupListGrid =  new Ext.grid.GridPanel({
		id: 'siteGroupListGrid',
		style:'border:1px solid #B5B8C8',
		height: 200,
		width: 538,
		deferRowRender:false,
		ctCls: 'grid',
		store: new Ext.data.ArrayStore({
			id: 'sitegroups',
			fields: ['id', 'code', 'name'],
			data: [
		      	{/literal}
		      	{section name=index loop=$storeGroups}
			    	{if $smarty.section.index.last}
						["{$storeGroups[index].id}", "{$storeGroups[index].code}", "{$storeGroups[index].name}"]
					{else}
						["{$storeGroups[index].id}", "{$storeGroups[index].code}", "{$storeGroups[index].name}"],
					{/if}
				{/section}
				{literal}
			]
		}),
		sm: siteGroupListCheckboxSelectionModelObj,
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				sortable: true,
				resizable: true
			},
			columns: [
				siteGroupListCheckboxSelectionModelObj,
				{header: 'ID', width: 30, dataIndex: 'id', sortable: true, hidden : true},
				{header: "{/literal}{#str_LabelCode#}{literal}", width: 100, dataIndex: 'code'},
				{header: "{/literal}{#str_LabelName#}{literal}", width: 100, dataIndex: 'name'}
			]
		}),
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		autoExpandColumn:3
	});

	var siteGroupContainer = { xtype:'panel', id: 'sitegroupcontainer', items: [siteGroupListGrid ], width: 538, fieldLabel: "{/literal}{#str_StoreGroups#}{literal}"};

	var orderValueRange = new Ext.form.ComboBox({
		id: 'ordervaluerange',
		name: 'ordervaluerange',
		mode: 'local',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		listeners:{
			select:{
				fn: rangeConfig
			}
		},
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelOrderValueRange#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: ['id', 'name'],
			data: [
					['', "{/literal}{#str_LabelNone#}{literal}"],
					['WITHOUTTAX', "{/literal}{#str_OrderValueRangeWITHOUTTAX#}{literal}"],
					['WITHTAX', "{/literal}{#str_OrderValueRangeWITHTAX#}{literal}"]

				]
		}),
		valueField: 'id',
		displayField: 'name',
		value: "{/literal}{$ordervaluerange}{literal}",
		useID: true,
		post: true
	});

	var includeDiscount = new Ext.form.Checkbox({
		id: 'ordervalueincludesdiscount',
		name: 'ordervalueincludesdiscount',
		boxLabel: "{/literal}{#str_LabelIncludeDiscount#}{literal}",
		{/literal}{if $ordervalueincludesdiscountchecked == 1}{literal}
			checked: true,
		{/literal}{else}{literal}
			checked: false,
		{/literal}{/if}{literal}
		post: true
	});

	var minValue = { xtype: 'textfield', id: 'orderminvalue', name: 'orderminvalue', value: "{/literal}{$orderminvalue}{literal}", validator: function(v){ return validate(v,true,false);  }, fieldLabel: "{/literal}{#str_LabelMinimumValue#}{literal}", hideLabel: false, post: true};
	var maxValue = { xtype: 'textfield', id: 'ordermaxvalue', name: 'ordermaxvalue', value: "{/literal}{$ordermaxvalue}{literal}", validator: function(v){ return validate(v,true,false);  },fieldLabel: "{/literal}{#str_LabelMaximumValue#}{literal}", hideLabel: false, post: true};

	{/literal}{if $optionms}{literal}
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		allowBlank: false,
		width:275,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		defvalue: "{/literal}{$companycode}{literal}",
		hideLabel:false,
		post:true,
		listeners:{
			select:{
				fn: function(comboBox, record, index){

					{/literal}{if $loginType == 0}{literal}
						shippingZonesCombo.clearValue();
						shippingZonesCombo.store.reload({
						params: { companycode: comboBox.getValue() }
						});
						productCombo.clearValue();
						productCombo.store.reload({
							params: { companycode: comboBox.getValue() }
							});

						licenseKeysGrid.store.reload({
							params: { companycode: comboBox.getValue() }
							});
					{/literal}{/if}{literal}

					}
				}
		},
		options: {
			ref: '{/literal}{$ref}{literal}',
			storeId: 'companyStore',
			includeGlobal: '1',
			onchange: function(){}
		}
	});
	{/literal}{/if}{literal}

	var dialogShippingRateFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
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

			{ xtype: 'panel', id: 'topPanel', layout: 'column',style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', columns: 2, plain:true, bodyBorder: false,
			border: false, 	defaults:{labelWidth: 70}, bodyStyle:'padding:5px 5px 0; border-top: 0px',
			items: [
				new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 195}, width:290,
				items:
					{
						id: 'code',
						name: 'code',
						allowBlank:false,
						maxLength: 20,
						{/literal}{if $isEdit == 1}{literal}
							style: {textTransform: "uppercase", background: "#c9d8ed"},
							readOnly: true,
			           		value: "{/literal}{$shippingratecode}{literal}",
			        	{/literal}{else}{literal}
			        		style: {textTransform: "uppercase"},
						{/literal}{/if}{literal}
						validationEvent:false,
						validateOnBlur: false,
						listeners:{	blur:{	fn: forceAlphaNumeric }	},
						fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
						post: true
					}
				})
				{/literal}{if $optionms}{literal}
					{/literal}{if !$companyLogin}{literal}
						,
						new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 250}, width:330,
							items:	companyCombo
						})
					{/literal}{/if}{literal}
				{/literal}{/if}{literal}
				,
				{ xtype: 'hidden', id: 'parentcode', name: 'parentcode', value: "{/literal}{$parentcode}{literal}",  post: true}
			]},



			{ /* tabpanel */
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				activeTab: 0,
				height: 329,
				shadow: true,
				plain:true,
				bodyBorder: false,
				layoutOnTabChange: true,
				border: true,
				style: 'margin-top: 7px',
				bodyStyle:'border-right: 1px solid #96bde7;',
				defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 170,  style:'padding:10px; background-color: #eaf0f8;'},
				items: [
					{
						title: "{/literal}{#str_LabelSettings#}{literal}",
						id: 'settingsTab',
						items:
						[
							{ xtype: 'panel', layout: 'form', width: 635,
								items:
								[
									shippingMethodsCombo,
									shippingZonesCombo,
									productCombo,
									orderValueRange,
									minValue,
									maxValue,
									includeDiscount
								]
							}
						]
					},
					{
						title: "{/literal}{#str_LabelLicenseKey#}{literal}",
						id: 'licenseTab',
						layout: 'form',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						items: [
						        	licenseKeysGrid
						        ]
					},
					{
						title: "{/literal}{#str_LabelAdditionalInformation#}{literal}",
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						items: [
						        	{ xtype: 'panel', width: 772, items: langPanel }

						        ]
					},{
						title: "{/literal}{#str_LabelShippingWeight#}{literal}",
						id: 'shipratestab',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						items: [
						        	taxCodeCombo,ShippingWeightInputPanel
						        ]
					}
					{/literal}{if $optioncfs}{literal}
						,
						{
							title: "{/literal}{#str_LabelCollectFromStore#}{literal}",
							id: 'collectfromstoretab',
							labelWidth:90,
							listeners: {
						        'afterlayout': {
						            fn: function(p)
									{
						                p.disable();
						                shippingMethodID = Ext.getCmp('shippingmethodcode').getValue();
						                shippingMethodIsCollectFromStore = Ext.getCmp('shippingmethodcode').store.getById(shippingMethodID);
						        		if ((shippingMethodIsCollectFromStore) && (shippingMethodIsCollectFromStore.data['collectfromstore'] == 1)) { p.enable(); }
						           },
						            single: true
						        },
						        'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }
						    },
							items:
							[
								{ xtype: 'panel', width: 634, layout: 'form',
									items:
									[
										{/literal}{if $optioncfs}{literal}
											siteGroupContainer,
											payInStoreOption
										{/literal}{/if}{literal}
									]
								}
							]
						}
						{/literal}{/if}{literal}
					]
			}]
	});

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 450,
		width: 820,
		title: "{/literal}{$title}{literal}",
		items: dialogShippingRateFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					shippingRatesEditWindowExists = false;
				}
			}
		},
		bodyBorder: false,
		padding:0,
		cls: 'left-right-buttons',
		buttons:
		[
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
   	   			ctCls: 'width_100',
   	   			{/literal}{if $activechecked == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			}),
			{
	    		text: "{/literal}{#str_ButtonCancel#}{literal}",
	      		handler: function(){ gDialogObj.close();	},
	      		cls: 'x-btn-right'
	    	},
	    	{
	    		id: 'addEditButton',
	    		cls: 'x-btn-right',
	    		{/literal}{if $isEdit == 0}{literal}
					handler: addsaveHandler,
					text:"{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: editsaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
	    	}
		]
	});

	if ({/literal}{$isEdit}{literal} == 0)
	{

		var shippingMethodCount =  Ext.getCmp('shippingmethodcode').store.getCount();

		if (shippingMethodCount > 0)
		{
			var shippingMethodVal = Ext.getCmp('shippingmethodcode').store.getAt(0);
			Ext.getCmp('shippingmethodcode').setValue(shippingMethodVal.data['id']);
		}

		{/literal}{if $optioncfs}{literal}
			var payInStoreVal = Ext.getCmp('payinstoreoption').store.getAt(0);
			Ext.getCmp('payinstoreoption').setValue(payInStoreVal.data['id']);
		{/literal}{/if}{literal}

		Ext.getCmp('shippingzonecode').store.on({'load': setDefault });
		Ext.getCmp('productcode').store.on({'load': setDefault });

		{/literal}{if $optionms}{literal}
			Ext.getCmp('company').store.on({'load': function(){
				Ext.getCmp('company').setValue('GLOBAL')
				}
			});
		{/literal}{/if}{literal}

		rangeConfig('', '', 0);
	}
	else if({/literal}{$isEdit}{literal} == 1 && "{/literal}{$ordervaluerange}{literal}" == '')
	{
		rangeConfig('', '', 0);
	}


	var assignedLicenseKeys = new Array(
		{/literal}
		{section name=index loop=$assignedLicenseKeys}
		{if $smarty.section.index.last}
			"{$assignedLicenseKeys[index]}"
		{else}
			"{$assignedLicenseKeys[index]}",
		{/if}
		{/section}
		{literal}
	);

	var assignedSiteGroups = new Array(
		{/literal}
		{section name=index loop=$assignedSiteGroups}
		{if $smarty.section.index.last}
			"{$assignedSiteGroups[index]}"
		{else}
			"{$assignedSiteGroups[index]}",
		{/if}
		{/section}
		{literal}
	);

	if ({/literal}{$isEdit}{literal} == 1)
	{
		Ext.getCmp('shippingzonecode').store.on({'load': function(){Ext.getCmp('shippingzonecode').setValue("{/literal}{$shippingzone}{literal}")} });
		Ext.getCmp('productcode').store.on({'load': function(){Ext.getCmp('productcode').setValue("{/literal}{$productcode}{literal}")} });
	}

	{/literal}{if $isEdit == 0}{literal}
		shippingZonesCombo.store.reload({
		params: { companycode: '' }
		});
		productCombo.store.reload({
			params: { companycode: '' }
		});
		licenseKeysGrid.store.reload({
			params: { companycode: '' }
		});

	{/literal}{else}{literal}

		{/literal}{if $optionms}{literal}

		shippingZonesCombo.store.reload({
			params: { companycode: "{/literal}{$companycode}{literal}" }
			});
		productCombo.store.reload({
			params: { companycode: "{/literal}{$companycode}{literal}" }
			});
		licenseKeysGrid.store.reload({
			params: { companycode: "{/literal}{$companycode}{literal}" }
		});
		{/literal}{else}{literal}
			shippingZonesCombo.store.reload({
				params: { companycode: '' }
				});
			productCombo.store.reload({
				params: { companycode: '' }
				});
			licenseKeysGrid.store.reload({
				params: { companycode: '' }
				});
		{/literal}{/if}{literal}

	{/literal}{/if}{literal}

	Ext.getCmp('taxcode').store.on({
            'load': function() {
                Ext.getCmp('taxcode').setValue('{/literal}{$taxcode}{literal}');
            }
        });

	taxCodeStore.load();
	gDialogObj.show();

	if ({/literal}{$isEdit}{literal} == 1)
	{
		Ext.getCmp('licenseKeyGrid').store.on({'load': function(){licenseKeysCheckboxSelectionModelObj.selectRows(assignedLicenseKeys);} });
	}

	{/literal}{if $optioncfs}{literal}
		siteGroupListCheckboxSelectionModelObj.selectRows(assignedSiteGroups);
	{/literal}{/if}{literal}
}


{/literal}