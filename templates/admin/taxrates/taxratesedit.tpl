{literal}

function initialize(pParams)
{	
	{/literal}{$localizedcodesjavascript}{literal}
	{/literal}{$localizednamesjavascript}{literal}
	{/literal}{$languagecodesjavascript}{literal}
	{/literal}{$languagenamesjavascript}{literal}
	
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
        width: 450,
		data: {langList: langListStore, dataList: dataList},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 180, textCol: 207, delCol: 35},
			fieldWidth:  {langField: 175, textField: 182},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
	
	var LangContainer = {
            xtype: 'panel',
            width: 450,
        	bodyBorder: false,
            border:false,
            fieldLabel: "{/literal}{#str_LabelName#}{literal}",
            items: [
                langPanel
            ]
        };
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'taxRateForm',
        labelAlign: 'left',
        labelWidth:80,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
            { xtype: 'textfield', 
              id: 'code', 
              name: 'code', 
              allowBlank: false,
              maxLength: 20,
			width:250,
				{/literal}{if $isEdit == 1}{literal}
                  value: '{/literal}{$taxratecode}{literal}', 
                  style: {textTransform: "uppercase", background: "#dee9f6"},
                  readOnly: true,
              {/literal}{else}{literal}    
				style: {textTransform: "uppercase"},
		      {/literal}{/if}{literal}
              fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
              listeners:{
  				blur:{
  					fn: forceAlphaNumeric
  				}
  			},
              post: true
             },
            { 
            	 xtype: 'textfield', 
            	 id: 'rate', 
            	 name: 'rate', 
            	 value: '{/literal}{$taxrate}{literal}', 
            	 fieldLabel: "{/literal}{#str_LabelRate#}{literal}",
            	 validateOnBlur: true, 
            	 validator: function(v){ return validate(v,true,false);  }, 
            	 post: true,
            	 width:150
            },
            LangContainer
        ]
    });
    
    
    /* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminTaxRates.add&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('taxRateForm'), form = fp.getForm();
		submit = true;
		
		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			return false;
		}	
		
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, null, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
		}
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		
		var submitURL = 'index.php?fsaction=AdminTaxRates.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('taxRateForm'), form = fp.getForm();
		var submit = true;
	
		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			return false;
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
			gDialogObj.close();
		}
		else
		{
			icon = Ext.MessageBox.WARNING;
				
			Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: icon	});
		}
	}
    
    gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	title: "{/literal}{$title}{literal}",
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	 	resizable:false,
	  	layout: 'fit',
	  	height: 'auto',
	  	width: 570,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {   
				fn: function(){
    				taxRateEditWindowExists = false;
				}
			}
		},
	  	buttons: 
		[
			{ text: "{/literal}{#str_ButtonCancel#}{literal}", handler: function(){ gDialogObj.close(); } },
			{ text: "{/literal}{#str_ButtonAdd#}{literal}", id: 'addEditButton',
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

	
	var mainPanel = Ext.getCmp('dialog');
	mainPanel.show();	  
	

	function validate(value, allowDecimal, allowNegative)
	{
		if (!isNumeric(value, true, false))
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
}
{/literal}