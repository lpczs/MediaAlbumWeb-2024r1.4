{literal}
function initialize(pParams)
{
	var deleteImg = "{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png";
	var addImg = "{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png";
	var sessionId = "{/literal}{$session}{literal}";
	var companyCode = "{/literal}{$taxzonecompanycode}{literal}";
	var gTaxZoneID = "{/literal}{$taxzoneid}{literal}";
	var countryList = {/literal}{$fullCountryList}{literal};
	var isCompanyROW = {/literal}{$isCompanyROW}{literal};

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('code').getValue();
    	code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");
    	Ext.getCmp('code').setValue(code);
	}

	if (gTaxZoneID > 0)
	{
		var requestMode = 'EDIT';
	}
	else
	{
		var requestMode = 'ADD';
	}

	var countryPanel = new Ext.taopix.CountryPanel({
        id: 'countryPanel', name: 'countrycodes', post:true, height:190, width:449,
        data: { countryList: countryList },
        settings: {
            headers:      {coutryCodeLabel: "{/literal}{#str_LabelCode#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addImg},
            defaultText:  {coutryBlank: "{/literal}{#str_LabelSelectCountry#}{literal}" },
            columnWidth:  {codeCol: 150, delCol: 35},
            fieldWidth:   {coutryField: 176, regionField: 180},
            errorMsg:     {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"},
            ref:          sessionId,
            global:       true,
            requestType:  'TAXZONES',
            requestParam: gTaxZoneID,
            requestMode:  requestMode,
            companyCode: companyCode
        }
	});

	var countryContainer = {
        xtype: 'panel',
        width: 450,
       	bodyBorder: false,
        border:false,
        fieldLabel: "{/literal}{#str_LabelCountries#}{literal}",
        items: countryPanel
    };

	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:300,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		allowBlank:false,
		{/literal}{if $isEdit == 1 || $companyLogin}{literal}
			{/literal}{if $taxzonecompanycode == ""}{literal}
				defvalue: 'GLOBAL',
			{/literal}{else}{literal}
				defvalue: '{/literal}{$taxzonecompanycode}{literal}',
			{/literal}{/if}{literal}
		{/literal}{/if}{literal}
		options: {
			ref: '{/literal}{$ref}{literal}',
			storeId: 'companyStore',
			{/literal}{if $companyLogin}{literal}
			includeGlobal: '0',
			{/literal}{else}{literal}
			includeGlobal: '1',
			{/literal}{/if}{literal}
			includeShowAll: '0',
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = ''; countryPanel.reload(companyCode);}
		}
	});

	var taxCodeCombo1 = new Ext.form.ComboBox({
		id: 'taxlevel1',
		name: 'taxlevel1',
		mode: 'local',
		width:300,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelTaxCode1#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxcodestore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxcodelist1}
				{if $smarty.section.index.last}
					["{$taxcodelist1[index].code}", "{$taxcodelist1[index].name}"]
				{else}
					["{$taxcodelist1[index].code}", "{$taxcodelist1[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel1}{literal}',
		useID: true,
		post: true
	});

	var taxCodeCombo2 = new Ext.form.ComboBox({
		id: 'taxlevel2',
		name: 'taxlevel2',
		mode: 'local',
		width:300,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelTaxCode2#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxcodestore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxcodelist2}
				{if $smarty.section.index.last}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"]
				{else}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel2}{literal}',
		useID: true,
		post: true
	});


	var taxCodeCombo3 = new Ext.form.ComboBox({
		id: 'taxlevel3',
		name: 'taxlevel3',
		mode: 'local',
		width:300,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelTaxCode3#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxcodestore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxcodelist2}
				{if $smarty.section.index.last}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"]
				{else}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel3}{literal}',
		useID: true,
		post: true
	});

	var taxCodeCombo4 = new Ext.form.ComboBox({
		id: 'taxlevel4',
		name: 'taxlevel4',
		mode: 'local',
		width:300,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelTaxCode4#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxcodestore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxcodelist2}
				{if $smarty.section.index.last}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"]
				{else}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel4}{literal}',
		useID: true,
		post: true
	});

	var taxCodeCombo5 = new Ext.form.ComboBox({
		id: 'taxlevel5',
		name: 'taxlevel5',
		mode: 'local',
		width:300,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelTaxCode5#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxcodestore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxcodelist2}
				{if $smarty.section.index.last}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"]
				{else}
					["{$taxcodelist2[index].code}", "{$taxcodelist2[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel5}{literal}',
		useID: true,
		post: true
	});

	var shippingTaxCodeCombo = new Ext.form.ComboBox({
		id: 'shippingtaxcode',
		name: 'shippingtaxcode',
		mode: 'local',
		width:300,
		editable: false,
		allowBlank: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelShippingTaxCode#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'shippingtaxstore',
			fields: ['id', 'name'],
			data: [
					{/literal}
					{section name=index loop=$taxcodelist1}
					{if $smarty.section.index.last}

							["{$taxcodelist1[index].code}", "{$taxcodelist1[index].name}"]
					{else}
							["{$taxcodelist1[index].code}", "{$taxcodelist1[index].name}"],
					{/if}
					{/section}
					{literal}
				]
		}),
		valueField: 'id',
		displayField: 'name',
		{/literal}{if $isEdit == 1}{literal}
			value: '{/literal}{$shippingTaxCode}{literal}',
		{/literal}{/if}{literal}
		useID: true,
		post: true
	});

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'taxZonesForm',
        labelAlign: 'left',
        labelWidth:140,
        autoHeight: true,
        frame:true,
        bodyStyle:'padding-left:5px;',
        items: [
            {/literal}{if $companyLogin}{literal}
            	{/literal}{if !$companyHasROW && $isEdit == 0 }{literal}
		            {
						xtype: 'checkbox',
						id: 'isrestofworld',
						name: 'isrestofworld',
						boxLabel:"{/literal}{#str_LabelRestOfWorld#}{literal}",
						 listeners: {
							'check': {

							/* this 'scope' value is CRITICAL so that the event is fired in */
							/* the scope of the component, not the anonymous function...    */
								scope: this,
								fn: function(cb, checked){
									if (checked)
									{
										Ext.getCmp('code').disable();
										Ext.getCmp('code').setValue('');
										Ext.getCmp('countryPanel').store.loadData([]);
										Ext.getCmp('countryPanel').disable();
									}
									else
									{
										Ext.getCmp('code').reset();
										Ext.getCmp('code').enable();
										Ext.getCmp('countryPanel').reset();
										Ext.getCmp('countryPanel').enable();
									}
								}
							}
						},
						post: true
					},
				{/literal}{/if}{literal}
			{/literal}{/if}{literal}
            { xtype: 'textfield',
              id: 'code',
              name: 'code', width:300,
              {/literal}{if $isEdit == 1}{literal}
                  value: '{/literal}{$taxzonecode}{literal}',
                  style: {textTransform: "uppercase", background: "#dee9f6"},
                  readOnly: true,
              {/literal}{else}{literal}
				style: {textTransform: "uppercase"},
		      {/literal}{/if}{literal}
		      maxLength: 20,
              allowBlank:false,
              listeners:{
    				blur:{
    					fn: forceAlphaNumeric
    				}
    			},
              fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
              post: true},
            {
            	  xtype: 'textfield',
            	  id: 'name',
            	  name: 'name',
            	  allowBlank: false,
            	  width:300,
            	  maxLength: 50,
            	  {/literal}{if $isEdit == 1}{literal}
                    value: '{/literal}{$taxzonename}{literal}',
                  {/literal}{/if}{literal}
            	  fieldLabel: "{/literal}{#str_LabelName#}{literal}",
            	  post: true
            },
            {/literal}{if $optionms}{literal}
            	companyCombo,
        	{/literal}{/if}{literal}
            taxCodeCombo1,
            taxCodeCombo2,
            taxCodeCombo3,
            taxCodeCombo4,
            taxCodeCombo5,
            shippingTaxCodeCombo,
            countryContainer,
            { xtype: 'hidden', id: 'isdefault', name: 'isdefault', value: '{/literal}{$isdefault}{literal}', post: true},
            { xtype: 'hidden', id: 'hasRestOfWorld', name: 'hasRestOfWorld', value: '{/literal}{$companyHasROW}{literal}', post: true},
            { xtype: 'hidden', id: 'taxzonecodemain', name: 'taxzonecodemain', value: '{/literal}{$taxzonecodemain}{literal}', post: true}
        ]
    });


    /* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminTaxZones.add&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('taxZonesForm'), form = fp.getForm();
		var submit = true;
		var paramArray = new Object();
		paramArray['ordervalueincludesdiscount'] = '';
		paramArray['isrestofworld'] = '';
		var checkCountries = true;

		{/literal}{if $optionms}{literal}
		{/literal}{if $companyLogin}{literal}
			if (!Ext.getCmp('hasRestOfWorld').getValue())
			{
				if (Ext.getCmp('isrestofworld').checked)
				{
					paramArray['isrestofworld'] = '1';
					checkCountries = false;
				}
				else
				{
					paramArray['isrestofworld'] = '0';
					checkCountries = true;
				}
			}
		{/literal}{/if}{literal}
		{/literal}{/if}{literal}

		if (checkCountries)
		{
			var countriesValid = Ext.getCmp('countryPanel').isValid();
			if (countriesValid > 0)
			{
				switch (countriesValid)
				{
					case 1: Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: nlToBr("{/literal}{#str_ErrorNoCountries#}{literal}"), buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
						break;
					case 2: Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorDuplicateCountries#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
						break;
					case 3: Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorDuplicateCountriesCompany#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
						break;
				}
				submit = false;
			}
		}

		if(submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
		}
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));

		var submitURL = 'index.php?fsaction=AdminTaxZones.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('taxZonesForm'), form = fp.getForm();
		var submit = true;
		var paramArray = new Object();
		paramArray['ordervalueincludesdiscount'] = '';

		var countriesValid = Ext.getCmp('countryPanel').isValid();

		if (Ext.getCmp('isdefault').getValue())
		{
			submit = true;
		}
		else if((Ext.getCmp('countryPanel').isValid() > 0) && !Ext.getCmp('isdefault').getValue())
		{
			switch (countriesValid)
			{
				case 1: Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoCountries#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				break;
				case 2: Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorDuplicateCountries#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				break;
				case 3: Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorDuplicateCountriesCompany#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				break;
			}
			submit = false;
		}

		if(submit)
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
			gDialogObj.close();
		}
		else
		{
			icon = Ext.MessageBox.WARNING;
			Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg,	buttons: Ext.MessageBox.OK, icon: icon });
		}
	}

    /* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	title: "{/literal}{$title}{literal}",
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight:true,
	  	width: 630,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {
				fn: function(){
					taxZoneEditWindowExists = false;
				}
			}
		},
	  	buttons:
		[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}", handler: function(){gDialogObj.close();}	},
			{	text: "{/literal}{#str_ButtonAdd#}{literal}", id: 'addEditButton',
				{/literal}{if $isEdit == 0}{literal}
				handler: addsaveHandler,
				text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
				handler: editsaveHandler,
				text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

    {/literal}{if $isEdit == 0}{literal}
    	Ext.getCmp('taxlevel1').setValue(Ext.getCmp('taxlevel1').store.getAt(0).data.id);
    {/literal}{/if}{literal}

	{/literal}{if $isEdit == 1}{literal}
	if (isCompanyROW == 1)
	{
		Ext.getCmp('company').disable()
	}
	{/literal}{/if}{literal}

	{/literal}{if $isEdit != 1 && ! $companyLogin}{literal}
	Ext.getCmp('company').store.on({'load': function(){ Ext.getCmp('company').setValue('GLOBAL'); } });
	{/literal}{/if}{literal}

	{/literal}{if $companyLogin}{literal}
		Ext.getCmp('company').disable();
	{/literal}{/if}{literal}

	{/literal}{if $isdefault}{literal}
		Ext.getCmp('countryPanel').disable();
	{/literal}{else}{literal}
		Ext.getCmp('countryPanel').enable();
	{/literal}{/if}{literal}

	var mainPanel = Ext.getCmp('dialog');
	mainPanel.show();

}
{/literal}