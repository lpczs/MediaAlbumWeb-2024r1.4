{literal}

function initialize(pParams)
{
	{/literal}{$localizedcodesjavascript}{literal}
	{/literal}{$localizednamesjavascript}{literal}
	{/literal}{$languagecodesjavascript}{literal}
	{/literal}{$languagenamesjavascript}{literal}


	/* save functions */
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
			Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg, buttons: Ext.MessageBox.OK,	icon: Ext.MessageBox.WARNING });
		}
	}


	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminCurrencies.add&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('currencyForm'), form = fp.getForm();

		var paramArray = new Object();
		paramArray['symbolatfront'] = '';

		if (Ext.getCmp('symbolatfront').checked)
		{
			paramArray['symbolatfront'] = '1';
		}
		else
		{
			paramArray['symbolatfront'] = '0';
		}

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.minWidth = 250;
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			return false;
		}
		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, '{/literal}{#str_MessageSaving#}{literal}', saveCallback);
	}


	function editSaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));

		var submitURL = 'index.php?fsaction=AdminCurrencies.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('currencyForm'), form = fp.getForm();
		var paramArray = new Object();
		paramArray['symbolatfront'] = '';

		if (Ext.getCmp('symbolatfront').checked)
		{
			paramArray['symbolatfront'] = '1';
		}
		else
		{
			paramArray['symbolatfront'] = '0';
		}

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.minWidth = 250;
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			return false;
		}

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, '{/literal}{#str_MessageSaving#}{literal}', saveCallback);
	}


	function setExchangeRateDate()
	{
		var exchangeRate = Ext.getCmp('exchangerate').getValue();

		var d = new Date();
		var excahngeRateDateSet = formatDate(d,'yyyy-MM-dd HH:mm:ss');

		Ext.getCmp('exchangeratedateset').setValue(excahngeRateDateSet);
	}


	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('code').getValue();

    	code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");

   	 	Ext.getCmp('code').setValue(code);
	}


	function validateISO(value)
	{
		if (value.length != 3)
		{
			msg = nlToBr('{/literal}{#str_ErrorNoISONumber#}{literal}');
			return msg;
		}
		else
		{
			return true;
		}
	}

	function validate(value, allowDecimal, allowNegative)
	{
		if (! isNumeric(value, allowDecimal, allowNegative))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	var str_LabelLanguageName    = "{/literal}{#str_LabelLanguageName#}{literal}";
	var str_localizedNameLabel   = "{/literal}{#str_LabelName#}{literal}";

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
    };

    var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel',
		name: 'name',
		height:150,
		post: true,
		data: {langList: langListStore, dataList: dataList},
		style: 'border:1px solid #b4b8c8',
		width: 450,
		settings:
		{
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: '{/literal}{#str_LabelSelectLanguage#}{literal}',  textBlank: '{/literal}{#str_ExtJsTypeValue#}{literal}', defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 190, textCol: 198, delCol: 35},
			fieldWidth:  {langField: 179, textField: 178},
			errorMsg:    {blankValue: '{/literal}{#str_ExtJsTextFieldBlank#}{literal}'}
		}
	});

	var LangContainer = {
    	xtype: 'panel',
        width: 450,
        fieldLabel: '{/literal}{#str_LabelName#}{literal}',
        items: langPanel
    };

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'currencyForm',
        labelAlign: 'left',
        labelWidth:110,
        autoHeight: true,
        frame:true,
        bodyStyle:'padding-left:5px;',
        items: [
            { xtype: 'textfield',
              id: 'code',
              name: 'code',
              allowBlank:false,
              maxLength: 20,
				{/literal}{if $isEdit == 1}{literal}
                  value: '{/literal}{$code}{literal}',
                  style: {textTransform: "uppercase", background: "#dee9f6"},
                  readOnly: true,
              {/literal}{else}{literal}
				style: {textTransform: "uppercase"},
              {/literal}{/if}{literal}
              fieldLabel: '{/literal}{#str_LabelISOCode#}{literal}',
              listeners:{
	  				blur:{
	  					fn: forceAlphaNumeric
	  				}
	  			},
              post: true
            },
            LangContainer,
            {
	            xtype: 'textfield',
	            id: 'isonumber',
	            name: 'isonumber',
	            {/literal}{if $isEdit == 1}{literal}
	              	value: '{/literal}{$isonumber}{literal}',
	            {/literal}{/if}{literal}
	            fieldLabel: '{/literal}{#str_LabelISONumber#}{literal}',
	            validator: function(v){ return validateISO(v);},
	            validateOnBlur: true,
	            post: true
            },
            {
	            xtype: 'textfield',
	            id: 'symbol',
	            name: 'symbol',
	            allowBlank:false,
	            {/literal}{if $isEdit == 1}{literal}
	              	value: '{/literal}{$symbol}{literal}',
	            {/literal}{/if}{literal}
	            fieldLabel: '{/literal}{#str_LabelSymbol#}{literal}',
	            validateOnBlur: true,
	            post: true
            },
            { xtype: 'checkbox', id: 'symbolatfront', name: 'symbolatfront',
            	{/literal}{if $symbolatfrontchecked == 1}{literal}
            		checked: true,
            	{/literal}{else}{literal}
            		checked: false,
            	{/literal}{/if}{literal}
            	boxLabel: "{/literal}{#str_LabelSymbolAtFront#}{literal}", post: true},
            { xtype: 'textfield', id: 'decimalplaces', name: 'decimalplaces', value: '{/literal}{$decimalplaces}{literal}', fieldLabel: '{/literal}{#str_LabelDecimalPlaces#}{literal}', validateOnBlur: true, validator:function(v) { return validate(v,false,false);  }, post: true},
            { xtype: 'textfield',
              id: 'exchangerate',
              name: 'exchangerate',
              allowBlank: false,
              value: '{/literal}{$exchangerate}{literal}',
              fieldLabel: '{/literal}{#str_LabelExchangeRate#}{literal}',
              validateOnBlur: true,
              validator:function(v){ return validate(v,true,false);},
              post: true,
              {/literal}{if $isdefault == 1}{literal}
	              disabled: true,
	          {/literal}{else}{literal}
            	  disabled: false,
              {/literal}{/if}{literal}
              listeners: { blur:{ fn: setExchangeRateDate }	}
            },
            { xtype: 'textfield', readOnly: true, style: { background: "#dee9f6"}, id: 'exchangeratedateset', name: 'exchangeratedateset', value: '{/literal}{$exchangeratedateset}{literal}', fieldLabel: '{/literal}{#str_LabelExchangeRateSet#}{literal}', post: true}
        ]
    });

    /* create modal window for add and edit */
	var gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		title: "{/literal}{$title}{literal}",
		resizable:false,
		layout: 'fit',
		height: 'auto',
		width: 600,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
		currencyEditWindowExists = false;
				}
			}
		},
		buttons:
		[
			{ text: '{/literal}{#str_ButtonCancel#}{literal}', handler: function(){ gDialogObj.close();	} },
			{ text: '{/literal}{#str_ButtonAdd#}{literal}', id: 'addEditButton',
				{/literal}{if $isEdit == 0}{literal}
					handler: addsaveHandler,
					text: '{/literal}{#str_ButtonAdd#}{literal}'
				{/literal}{else}{literal}
					handler: editSaveHandler,
					text: '{/literal}{#str_ButtonUpdate#}{literal}'
				{/literal}{/if}{literal}
			}
		]
	});
	gDialogObj.show();
}

{/literal}