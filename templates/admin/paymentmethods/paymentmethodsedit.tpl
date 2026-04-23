{literal}
{/literal}{$localizedcodesjavascript}{literal}
{/literal}{$localizednamesjavascript}{literal}
{/literal}{$languagecodesjavascript}{literal}
{/literal}{$languagenamesjavascript}{literal}

function initialize(pParams)
{
	var str_LabelLanguageName    = "{/literal}{#str_LabelLanguageName#}{literal}";
	var str_localizedNameLabel   = "{/literal}{#str_LabelName#}{literal}";
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	var langListStore = [];
	var dataList = [  ];
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

    /* COMPONENTS */
	var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel',
		name:'name',
		height:150,
		post:true,
		style:'border:1px solid #96bde7',
		data: {langList: langListStore, dataList: dataList},
		settings:
		{
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 150,   textCol: 227, delCol: 35},
			fieldWidth:  {langField: 145, textField: 204},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});

	var LangContainer = {
            xtype: 'panel',
            width:440,
            fieldLabel: "{/literal}{#str_LabelName#}{literal}",
            items: [
                langPanel
            ]
        };

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'paymentMethodsForm',
		header: false,
		frame:true,
		autoWidth: true,
		autoHeight:true,
		layout: 'form',
		defaultType: 'textfield',
		bodyBorder: false,
		border: false,
		defaults: {labelWidth:60},
		labelWidth:60,
        items: [
            {
				xtype: 'textfield',
				id: 'code',
				name: 'code',
				width: 250,
				maxLength: 20,
				value: "{/literal}{$code}{literal}",
				allowBlank:false,
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
				style: {textTransform: "uppercase", background: "#dee9f6"},
				readOnly: true,
				post: true
			},
        	LangContainer,
			{
				xtype: 'checkbox',
				id: 'availablewhenshipping',
				name: 'availablewhenshipping',
				boxLabel: "{/literal}{#str_LabelAvailableWhenShipping#}{literal}",
				{/literal}{if $availablewhenshippingchecked == 1}{literal}
					checked: true,
				{/literal}{else}{literal}
					checked: false,
				{/literal}{/if}{literal}
				post: true
			},
			{
				xtype: 'checkbox',
				id: 'availablewhennotshipping',
				name: 'availablewhennotshipping',
				boxLabel: "{/literal}{#str_LabelAvailableWhenNotShipping#}{literal}",
				{/literal}{if $availablewhennotshippingchecked == 1}{literal}
					checked: true,
				{/literal}{else}{literal}
					checked: false,
				{/literal}{/if}{literal}
				post: true
			}
        ]
    });

	{/literal}{if $PIS == 1}{literal}
		Ext.getCmp('availablewhennotshipping').disable();
		Ext.getCmp('availablewhenshipping').disable();
	{/literal}{/if}{literal}

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		closeAction: 'hide',
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		listeners: {
			'close': {
				fn: function(){
					paymentMethodsEditWindowExists = false;
				}
			}
		},
		layout: 'fit',
		height: 315,
		width: 550,
		items: dialogFormPanelObj,
		cls: 'left-right-buttons',
		buttons:
		[
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
				ctCls: 'width_100'
			}),
			{ text: '{/literal}{#str_ButtonCancel#}{literal}', handler: function(){ gDialogObj.close();	} },
			{
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				handler: editsaveHandler,
				cls: 'x-btn-right'
			}
		]
	});

	{/literal}{if $activechecked == 1}{literal}
		Ext.getCmp('isactive').setValue(true);
	{/literal}{else}{literal}
		Ext.getCmp('isactive').setValue(false);
	{/literal}{/if}{literal}

	gDialogObj.show();

}
{/literal}