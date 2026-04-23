{literal}

function initialize(pParams)
{	
 	{/literal}
 	var country_default = "{$homecountrycode}";
	var language_default = "{$defaultlanguagecode}";
	var currency_default = "{$defaultcurrencycode}";
	var creditlimit_default = "{$creditlimit}";
	var taxaddress_default = "{$taxaddress}";
	var maxloginattempts_default = "{$maxloginattempts}";
	var accountlockouttime_default = "{$accountlockouttime}";
	var maxiploginattempts_default = "{$maxiploginattempts}";
	var maxiploginattemptsminutes_default = "{$maxiploginattemptsminutes}";
	var minpasswordscore_default = "{$minpasswordscore}"
	var customerupdateauthrequired_default = "{$customerupdateauthrequired}";
	{literal}

	// Options for the Customer Update Authentication dropdown
	var customerUpdateAuthData = new Ext.data.ArrayStore({
		id: 1,
		fields: ['storeID', 'storeName'],
		data: [
			[0, "{/literal}{#str_LabelOff#}{literal}"],
			[1, "{/literal}{#str_LabelOn#}{literal}"]
		]
	});

	// Options for the password score combo box.
	var passwordScoreData = new Ext.data.ArrayStore({
		id: 0,
		fields: ['storeID', 'storeName'],
		data: [
			[0, "{/literal}{#str_LabelOff#}{literal}"],
			[2, "{/literal}{#str_LabelPasswordMedium#}{literal}"],
			[3, "{/literal}{#str_LabelPasswordStrong#}{literal}"],
			[4, "{/literal}{#str_LabelPasswordVeryStrong#}{literal}"]
		]
	});

 	var resetForm_fnc = function()
 	{
 		Ext.getCmp('resetbutton').setDisabled(true);
 		Ext.getCmp('updatebutton').setDisabled(true);

 		Ext.getCmp('location').setValue(country_default);
 		Ext.getCmp('language').setValue(language_default);
 		Ext.getCmp('currency').setValue(currency_default);
 		Ext.getCmp('creditlimit').setValue(creditlimit_default);
 		Ext.getCmp('maxloginattempts').setValue(maxloginattempts_default);
 		Ext.getCmp('accountlockouttime').setValue(accountlockouttime_default);
 		Ext.getCmp('maxiploginattempts').setValue(maxiploginattempts_default);
 		Ext.getCmp('maxiploginattemptsminutes').setValue(maxiploginattemptsminutes_default);
		Ext.getCmp('minpasswordscore').setValue(minpasswordscore_default);
		Ext.getCmp('customerupdateauthrequired').setValue(customerupdateauthrequired_default);

		// Reset the minimum value of the maxiploginattempts to be more than the number entered as the Max login attempts. Then re-validate field.
		Ext.getCmp('maxiploginattempts').setMinValue(parseInt(maxloginattempts_default) + 1);
		Ext.getCmp('maxiploginattempts').validate();

		// Minimum account lockout time must be larger than the failed IP login attempts or the account lockout will get into a loop.
		Ext.getCmp('maxiploginattemptsminutes').setMaxValue(parseInt(accountlockouttime_default));
		Ext.getCmp('maxiploginattemptsminutes').validate();

		if (taxaddress_default == 0)
		{
			Ext.getCmp('taxbill').setValue(true);
			Ext.getCmp('taxship').setValue(false);
		}
		else
		{
			Ext.getCmp('taxship').setValue(true);
			Ext.getCmp('taxbill').setValue(false);
		}
	};
	
	var formChanged = function()
	{
		var maxloginattempts = Ext.getCmp('maxloginattempts').getValue();
		var accountlockouttime = Ext.getCmp('accountlockouttime').getValue();

		// Reset the minimum value of the maxiploginattempts to be more than the number entered as the Max login attempts. Then re-validate field.
		Ext.getCmp('maxiploginattempts').setMinValue(maxloginattempts + 1);
		Ext.getCmp('maxiploginattempts').validate();

		// Minimum account lockout time must be larger than the failed IP login attempts or the account lockout will get into a loop.
		Ext.getCmp('maxiploginattemptsminutes').setMaxValue(accountlockouttime);
		Ext.getCmp('maxiploginattemptsminutes').validate();

		Ext.getCmp('resetbutton').setDisabled(false);
		Ext.getCmp('updatebutton').setDisabled(false);
	};
	
	var onPostCallback = function()
	{
		country_default = Ext.getCmp('location').getValue();
		language_default = Ext.getCmp('language').getValue();
		currency_default = Ext.getCmp('currency').getValue();
		creditlimit_default = Ext.getCmp('creditlimit').getValue();
		taxaddress_default = Ext.getCmp('taxship').getValue();
		maxloginattempts_default = Ext.getCmp('maxloginattempts').getValue();
		accountlockouttime_default = Ext.getCmp('accountlockouttime').getValue();
		maxiploginattempts_default = Ext.getCmp('maxiploginattempts').getValue();
		maxiploginattemptsminutes_default = Ext.getCmp('maxiploginattemptsminutes').getValue();
		minpasswordscore_default = Ext.getCmp('minpasswordscore').getValue();
		customerupdateauthrequired_default = Ext.getCmp('customerupdateauthrequired').getValue();

		Ext.getCmp('resetbutton').setDisabled(true);
		Ext.getCmp('updatebutton').setDisabled(true);
	};
	
	var updateFunction = function(btn,ev)
	{ 
		var theForm = Ext.ComponentMgr.get('constantsForm').getForm(); 
		var params = [];
		
		params['taxaddress'] = (Ext.getCmp('taxbill').checked) ? '0' : '1';

		var firstInvalid = theForm.items.find(function(f) { return !f.isValid(); });

        if (firstInvalid) 
		{
			var invalidOwnerID = firstInvalid.ownerCt.getId();
			var theTabPanel = Ext.getCmp('maintabpanel');

			var invalidOwner = Ext.getCmp(invalidOwnerID);
			var tabToSwitchToID = invalidOwner.ownerCt.getId();

			if (tabToSwitchToID == 'maintabpanel')
			{
				tabToSwitchToID = invalidOwnerID;
			}

			theTabPanel.setActiveTab(tabToSwitchToID);

			firstInvalid.focus();
		}
		else
		{
			Ext.taopix.formPanelPost(Ext.ComponentMgr.get('constantsForm'), theForm, params, './?fsaction=AdminConstants.edit&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", onPostCallback);
		}
		
		return;
	};
	
	var currencyField = Ext.extend(Ext.form.NumberField, 
	{
        setValue : function(v){
            v = typeof v == 'number' ? v : String(v).replace(this.decimalSeparator, ".");
            v = isNaN(v) ? '' : String(v).replace(".", this.decimalSeparator);
            v = isNaN(v) ? '' : this.fixPrecision(String(v).replace(".", this.decimalSeparator));
            return Ext.form.NumberField.superclass.setValue.call(this, v);
        },
        fixPrecision : function(value){
            var nan = isNaN(value);
            if(!this.allowDecimals || this.decimalPrecision == -1 || nan || !value){
               return nan ? '' : value;
            }
            return parseFloat(value).toFixed(this.decimalPrecision);
        }
    });


	function setFormNumberField(pOptName, pOptTitle, pOptValue, pOptMin)
	{
		var optionContainer = new Ext.form.NumberField({
			xtype: 'numberfield',
			id: pOptName,
			name: pOptName,
			width: 100,
			labelStyle: 'width: 264px;',
			allowBlank: false,
			allowDecimals: false,
			allowNegative: false,
			value: pOptValue,
			minValue: pOptMin,
			fieldLabel: pOptTitle,
			listeners: { change: formChanged },
			validateOnBlur: true,
			post: true
		});

		return optionContainer;
	}

	function setFormLabelField(pID, pLabelText)
	{
		var labelContainer = new Ext.Container({
			style: 'padding: 0px 0px 8px 0px;',
			items:
			[
				{
					id: pID,
					xtype: 'label',
					text: pLabelText
				}
			]
		});
		return labelContainer;
	}

	function setFormComboBox(pID, pLabel, pValue, pData, pLabelWidth, pComboWidth)
	{
		var comboContainer = new Ext.form.ComboBox({
			id: pID,
			name: pID,
			width: pComboWidth,
			value: pValue,
			labelStyle: 'width: ' + pLabelWidth + 'px;',
			fieldLabel: pLabel,
			valueField: 'storeID',
			displayField: 'storeName',
			store: pData,
			mode: 'local',
			allowBlank: false,
			forceSelection: true,
			editable: false,
			validationEvent: false,
			listeners: {select: formChanged},
			useID: true,
			post: true,
			triggerAction: 'all',
		});

		return comboContainer;
	};

	var constantsFormTabPanelObj = new Ext.FormPanel({
        id: 'constantsForm',
        frame: true,
        layout: 'form',
        defaults: { labelWidth: 120 },
        items: [
			{
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				layoutOnTabChange: true,
				activeTab: 0,
				autoWidth: true,
				height: 617,
				shadow: true,
				plain: true,
				defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 140, bodyStyle:'padding:10px; background-color: #eaf0f8;'},
				items: [
					{
						title: "{/literal}{#str_TitleSystemTab#}{literal}",
						items: [
							new Ext.form.ComboBox({
								id: 'location',
								name: 'location',
								hiddenName: 'country',
								width:300,
								labelStyle: 'width:155px;',
								allowBlank: false,
								forceSelection: true,
								editable: false,
								mode: 'local',
								hiddenId:'location_hi',
								store: new Ext.data.ArrayStore({ id: 0, fields: ['countrycode', 'countryname'], data: {/literal}{$countrylist}{literal} }),
								validationEvent:false,
								valueField: 'countrycode',
								displayField: 'countryname',
								listeners :{select: formChanged},
								useID: true,
								post: true,
								fieldLabel: '{/literal}{#str_LabelLocation#}{literal}',
								triggerAction: 'all',
								value: country_default
							}),
							new Ext.form.ComboBox({
								id: 'language',
								name: 'language',
								hiddenName: 'lang',
								width:200,
								labelStyle: 'width:155px;',
								allowBlank: false,
								forceSelection: true,
								editable: false,
								mode: 'local',
								hiddenId:'language_hi',
								store: new Ext.data.ArrayStore({ id: 0, fields: ['lang_id', 'lang_name'], data: {/literal}{$languagelist}{literal} }),
								validationEvent:false,
								valueField: 'lang_id',
								displayField: 'lang_name',
								listeners :{select: formChanged},
								useID: true,
								post: true,
								fieldLabel:'{/literal}{#str_LabelLanguage#}{literal}',
								triggerAction: 'all',
								value: language_default
							}),
							new Ext.form.ComboBox({
								id: 'currency',
								name: 'currency',
								hiddenName: 'curr',
								width:200,
								labelStyle: 'width:155px;',
								allowBlank: false,
								forceSelection: true,
								editable: false,
								mode: 'local',
								hiddenId:'currency_hi',
								store: new Ext.data.ArrayStore({ id: 0, fields: ['format_id', 'format_name'], data: {/literal}{$currencylist}{literal} }),
								validationEvent:false,
								valueField: 'format_id',
								displayField: 'format_name',
								listeners :{select: formChanged},
								useID: true,
								post: true,
								fieldLabel:'{/literal}{#str_LabelCurrency#}{literal}',
								triggerAction: 'all',
								value: currency_default
							}),

							{
								xtype: 'numberfield',
								id: 'creditlimit',
								name: 'creditlimit',
								width: 100,
								labelStyle: 'width:155px;',
								allowBlank: false,
								allowDecimals: true,
								allowNegative: false,
								value: creditlimit_default,
								fieldLabel: "{/literal}{#str_LabelDefaultCreditLimit#}{literal}",
								listeners :{change: formChanged},
								validateOnBlur: true,
								post: true,
								setValue : function(v)
								{
									v = typeof v == 'number' ? v : String(v).replace(this.decimalSeparator, ".");
									v = isNaN(v) ? '' : this.fixPrecision(v);
									v = isNaN(v) ? '' : String(v).replace(".", this.decimalSeparator);
									return Ext.form.NumberField.superclass.setValue.call(this, v);
								},
								fixPrecision : function(value)
								{
									var nan = isNaN(value);
									if(!this.allowDecimals || this.decimalPrecision == -1 || nan || !value)
									{
									   return nan ? '' : value;
									}
									return parseFloat(value).toFixed(this.decimalPrecision);
								}
							},
							{
								xtype: 'radiogroup',
								labelStyle: 'width:155px;',
								columns: 1,
								fieldLabel: '{/literal}{#str_LabelTaxCalculationBy#}{literal}',
								autoWidth: true,
								items: [
									{boxLabel: '{/literal}{#str_LabelBillingAddress#}{literal}', name: 'taxaddress', inputValue: 0, id: 'taxbill', checked: {/literal}{if $taxaddress==0}true{else}false{/if}{literal}, listeners :{check: formChanged}},
									{boxLabel: '{/literal}{#str_LabelShippingAddress#}{literal}', name: 'taxaddress', inputValue: 1, id: 'taxship', checked: {/literal}{if $taxaddress==1}true{else}false{/if}{literal}, listeners :{check: formChanged}}
								]
							}
						]
					},
					{
						title: "{/literal}{#str_TitleSecurityTab#}{literal}",
						items: [
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_TitleAccountLockdown#}{literal}",
								items: [
									setFormLabelField('lockoutlabel', "{/literal}{#str_LabelLockout#}{literal}"),
									setFormNumberField('accountlockouttime', "{/literal}{#str_LabelAccountLockoutTime#}{literal}", accountlockouttime_default, 1)
								]
							},
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_TitleFailedUserLogin#}{literal}",
								items: [
									setFormLabelField('faileduserlabel', "{/literal}{#str_LabelFailedUserDescription#}{literal}"),
									setFormNumberField('maxloginattempts', "{/literal}{#str_LabelMaximumLoginAttempts#}{literal}", maxloginattempts_default, 3)
								]
							},
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_TitleFailedIPLogin#}{literal}",
								items: [
									setFormLabelField('failediplabel', "{/literal}{#str_LabelFailedIPDescription#}{literal}"),
									setFormNumberField('maxiploginattempts', "{/literal}{#str_LabelMaximumLoginAttempts#}{literal}", maxiploginattempts_default, 5),
									setFormNumberField('maxiploginattemptsminutes', "{/literal}{#str_LabelNumberOfMinutes#}{literal}", maxiploginattemptsminutes_default, 1)
								]
							},
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_TitleAllowedIPAddresses#}{literal}",
								items: [
									setFormLabelField('accesslistlabel', "{/literal}{#str_LabelAccessList#}{literal}"),
									{
										xtype: 'textarea',
										id: 'ipaccesslist',
										name: 'ipaccesslist',
										fieldLabel: "{/literal}{#str_LabelIPAddresses#}{literal}",
										width: 230,
										labelStyle: 'width:230px;',
										height: 50,
										post: true,
										maskRe: /^(\d{1,3})|(\.)|(,)|(\s)|(\*)$/,
										value: "{/literal}{$defaultipaccesslist}{literal}",
										enableKeyEvents: true,
										listeners :{change: formChanged, keydown: formChanged}
									}
								]
							},
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_TitleMinPasswordScore#}{literal}",
								items: [
									setFormLabelField('minpasswordscorelabel', "{/literal}{#str_LabelMinPasswordScoreDescription#}{literal}"),
									setFormComboBox('minpasswordscore', "{/literal}{#str_LabelMinPasswordScore#}{literal}", minpasswordscore_default, passwordScoreData, 231, 200)
								]
							},
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_TitleCustomerUpdateAuthRequired#}{literal}",
								items: [
									setFormLabelField('customerupdateauthrequiredlabel', "{/literal}{#str_LabelCustomerUpdateAuthRequiredDescription#}{literal}"),
									setFormComboBox('customerupdateauthrequired', "{/literal}{#str_LabelCustomerUpdateAuthRequired#}{literal}", customerupdateauthrequired_default, customerUpdateAuthData, 231, 200)
								]
							}
						]
					}
				]
			}
		]
	});
    
	gMainWindowObj = new Ext.Window({
		  id: 'MainWindow',
		  title: "{/literal}{#str_SectionTitleConstants#}{literal}",
		  closable:true,
		  width: 750,
		  height: 700,
		  layout: 'fit',
		  resizable:false,
		  padding:0, margin:0,
		  baseParams: { ref: '{/literal}{$ref}{literal}' },
		  items: constantsFormTabPanelObj,
		  listeners: {
				'close': {   
					fn: function(){
						accordianWindowInitialized = false;
					}
				}
			},
	      buttons: [{ id: 'resetbutton', text: '{/literal}{#str_ButtonReset#}{literal}', disabled: true, handler:resetForm_fnc }, 
	                { id: 'updatebutton', text: '{/literal}{#str_ButtonUpdate#}{literal}', disabled: true, handler:updateFunction }]
	});

	gMainWindowObj.show();
}

/* close this window panel */
function windowClose()
{
	if ((gMainWindowObj) && (gMainWindowObj.close))
	{
		gMainWindowObj.close();
	}
}

{/literal}