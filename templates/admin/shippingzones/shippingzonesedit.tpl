{literal}
function initialize(pParams)
{
	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminShippingZones.add&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('shippingZonesForm'), form = fp.getForm();
		var submit = true;
		var checkCountries = true;
		var paramArray = new Object();
		paramArray['isrestofworld'] = '';

		{/literal}{if $optionms || $optioncfs}{literal}
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
		var submitURL = 'index.php?fsaction=AdminShippingZones.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('shippingZonesForm'), form = fp.getForm();

		var submit = true;

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

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, null, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
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

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('code').getValue();
   	    code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");
   	    Ext.getCmp('code').setValue(code);
	}

	var deleteImg = "{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png";
	var addImg = "{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png";
	var sessionId = "{/literal}{$session}{literal}";
	var companyCode = "{/literal}{$shippingzonecompanycode}{literal}";
	var gTaxZoneID = "{/literal}{$shippingzoneid}{literal}";
	var countryList = {/literal}{$fullCountryList}{literal};
	var isCompanyROW = {/literal}{$isCompanyROW}{literal};

	var countryPanel = new Ext.taopix.CountryPanel({
    	id: 'countryPanel', name: 'countrycodes', post:true, height:235, width:439,
        data: { countryList: countryList },
        style: 'border:1px solid #b4b8c8',
        settings: {
            headers:      {coutryCodeLabel: "{/literal}{#str_LabelCode#}{literal}", textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addImg},
            defaultText:  {coutryBlank: "{/literal}{#str_LabelSelectCountry#}{literal}" },
            columnWidth:  {codeCol: 160, delCol: 35},
            fieldWidth:   {coutryField: 160, regionField: 185},
            errorMsg:     {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"},
            ref:          sessionId,
            global:       true,
            requestType:  'SHIPPINGZONES',
            requestParam: gTaxZoneID,
            requestMode:  'EDIT',
            companyCode: companyCode
        },
        onDeleteRecord: deletRecordCountry
	});



    function deletRecordCountry(countryCode)
    {
        var countryCombo = this.getBottomToolbar().items.items[0];
        var regionCombo = this.getBottomToolbar().items.items[2];

        var shortCode = countryCode.split('_')[0];

        var countryInfo = this.getRecordByCountryCode(this.bufData, shortCode);
        var recordPos = this.store.findExact('cCode',countryCode);
        var record = this.store.getAt(recordPos);

        this.store.removeAt(recordPos);
        this.store.sort('cCode','ASC');

        regionCombo.store.loadData(this.filterRegionList(shortCode, this.getRecordByCountryCode(this.bufData, shortCode).regions));
        this.removeGlobalRegions(this.data.removeList.split(','), regionCombo);
        regionCombo.reset();
        this.addAll(this, shortCode);

        if ((countryCombo.store.findExact('id',shortCode) < 0))
        {
            if ((countryInfo.hasRegions <= 0) || ((countryInfo.hasRegions > 0) && (regionCombo.store.data.items.length > 0)))
            {
                var r = new countryCombo.store.recordType({
                    no: countryInfo.no,
                    id: shortCode,
                    name: countryInfo.name,
                    blankText: countryInfo.regionBlankText,
                    hasRegions: countryInfo.hasRegions
                });
                r.commit();
                countryCombo.store.add(r);
                countryCombo.store.sort('no', 'ASC');
            }
        }

        if (countryCode.indexOf("_") < 0)
        {
            var noAllArray = this.data.noAll.split(',');

            for (var j=0; j < noAllArray.length; j++)
            {
                if (noAllArray[j] == shortCode) noAllArray.splice(j,1);
            }
            this.data.noAll = noAllArray.join(',');
        }

        this.reload(companyCombo.getValue());
        return false;
    }


	var countryContainer = {
    	xtype: 'panel',
        width: 440,
        bodyBorder: false,
        border:false,
        fieldLabel: "{/literal}{#str_LabelCountries#}{literal}",
        items: countryPanel
	};

	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:275,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		allowBlank:false,
		post:true,
		{/literal}{if $isEdit == 1 || $companyLogin}{literal}
			{/literal}{if $shippingzonecompanycode == ""}{literal}
				defvalue: 'GLOBAL',
			{/literal}{else}{literal}
				defvalue: '{/literal}{$shippingzonecompanycode}{literal}',
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
			onchange: function(){countryPanel.reload(companyCombo.getValue());}
		}
	});

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'shippingZonesForm',
		header: false,
		frame:true,
		autoWidth: true,
		autoHeight:true,
		layout: 'form',
		defaultType: 'textfield',
		bodyBorder: false,
		border: false,
		defaults: {labelWidth:90},
		items: [
		{/literal}{if $companyLogin}{literal}
			{/literal}{if !$companyHasROW && $isEdit == 0}{literal}
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
              name: 'code',
              maxLength: 20,
              width:275,
              {/literal}{if $isEdit == 1}{literal}
                  value: '{/literal}{$shippingzonecode}{literal}',
                  style: {textTransform: "uppercase", background: "#dee9f6"},
                  readOnly: true,
              {/literal}{else}{literal}
				style: {textTransform: "uppercase"},
              {/literal}{/if}{literal}
              listeners:{ blur:{ fn: forceAlphaNumeric }},
              allowBlank:false,
              fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
              post: true
             },
            {
            	 xtype: 'textfield',
            	 id: 'name',
            	 name: 'name',
            	 maxLength: 50,
            	 width:275,
            	 {/literal}{if $isEdit == 1}{literal}
                     value: "{/literal}{$shippingzonename}{literal}",
                 {/literal}{/if}{literal}
            	 fieldLabel: "{/literal}{#str_LabelName#}{literal}",
            	 allowBlank: false,
            	 post: true
            },
            {/literal}{if $optionms}{literal}
    		companyCombo,
    		{/literal}{/if}{literal}
    		countryContainer,
    		 { xtype: 'hidden', id: 'isdefault', name: 'isdefault', value: '{/literal}{$isdefault}{literal}', post: true},
    		 { xtype: 'hidden', id: 'hasRestOfWorld', name: 'hasRestOfWorld', value: '{/literal}{$companyHasROW}{literal}', post: true},
    		 { xtype: 'hidden', id: 'shippingzonemain', name: 'shippingzonemain', value: '{/literal}{$shippingzonecodemain}{literal}', post: true}

        ]
    });

    gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		autoHeight: true,
		width: 580,
		bodyBorder: false,
		padding:0,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
    				shippingZonesEditWindowExists = false;
				}
			}
		},
		title: "{/literal}{$title}{literal}",
		buttons:
		[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ gDialogObj.close();	}
			},
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				id: 'addEditButton',
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

	{/literal}{if $companyLogin}{literal}
		Ext.getCmp('company').disable();
	{/literal}{else}{literal}
		{/literal}{if $isEdit == 0}{literal}
		Ext.getCmp('company').store.on({'load': function(){
			Ext.getCmp('company').setValue('GLOBAL')
			}
		});
		{/literal}{/if}{literal}
	{/literal}{/if}{literal}

	{/literal}{if $isdefault}{literal}
		Ext.getCmp('countryPanel').disable();
	{/literal}{else}{literal}
		Ext.getCmp('countryPanel').enable();
	{/literal}{/if}{literal}

	{/literal}{if $isEdit == 1}{literal}
		if (isCompanyROW == 1)
		{
			Ext.getCmp('company').disable()
		}
	{/literal}{/if}{literal}

	gDialogObj.show();
}

{/literal}