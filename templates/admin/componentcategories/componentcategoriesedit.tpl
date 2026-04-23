{literal}

function initialize(pParams)
{	
	{/literal}{$localizedcodesjavascript}{literal}
	{/literal}{$localizednamesjavascript}{literal}
	{/literal}{$languagecodesjavascript}{literal}
	{/literal}{$languagenamesjavascript}{literal}
	{/literal}{$sitegrouplocalizedcodesjavascript}{literal}
	{/literal}{$sitegrouplocalizednamesjavascript}{literal}
    
    var str_LabelLanguageName    = "{/literal}{#str_LabelLanguageName#}{literal}";
	var str_localizedNameLabel   = "{/literal}{#str_LabelName#}{literal}";
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	const TPX_COMPONENT_DISPLAY_STAGE_NONE = {/literal}{$TPX_COMPONENT_DISPLAY_STAGE_NONE}{literal};
	const TPX_COMPONENT_DISPLAY_STAGE_START = {/literal}{$TPX_COMPONENT_DISPLAY_STAGE_START}{literal};
	const TPX_COMPONENT_DISPLAY_STAGE_ORDER = {/literal}{$TPX_COMPONENT_DISPLAY_STAGE_ORDER}{literal};
	const TPX_COMPONENT_DISPLAY_STAGE_ALL = {/literal}{$TPX_COMPONENT_DISPLAY_STAGE_ALL}{literal};
	
	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminComponentCategories.add&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('componentCategoryForm'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
	
		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		paramArray['requirespagecount'] = '';
		
		if (Ext.getCmp('requirespagecount').checked)
		{
			paramArray['requirespagecount'] = '1';
		}
		else
		{
			paramArray['requirespagecount'] = '0';
		}

		paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_NONE;
		
		if (Ext.getCmp('onlinedisplaystageorder').checked && Ext.getCmp('onlinedisplaystageprojectstart').checked)
		{
			paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ALL;
		}
		else
		{
			if (Ext.getCmp('onlinedisplaystageorder').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ORDER;
			}
			else if (Ext.getCmp('onlinedisplaystageprojectstart').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_START;
			}
		}
				
		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			submit = false;
		}	
	
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
		}
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainComponentWindowObj.findById('maingrid'));
		
		var submitURL = 'index.php?fsaction=AdminComponentCategories.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('componentCategoryForm'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
	
		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		paramArray['requirespagecount'] = '';
		
		if (Ext.getCmp('requirespagecount').checked)
		{
			paramArray['requirespagecount'] = '1';
		}
		else
		{
			paramArray['requirespagecount'] = '0';
		}

		paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_NONE;
		
		if (Ext.getCmp('onlinedisplaystageorder').checked && Ext.getCmp('onlinedisplaystageprojectstart').checked)
		{
			paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ALL;
		}
		else
		{
			if (Ext.getCmp('onlinedisplaystageorder').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ORDER;
			}
			else if (Ext.getCmp('onlinedisplaystageprojectstart').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_START;
			}
		}
						
		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			submit = false;
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
			var gridObj = gMainComponentWindowObj.findById('maingrid');
			var dataStore = gridObj.store;	
		
			gridObj.store.reload();
			gDialogObj.close();
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
    
    var promptList = [];
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
    		promptList.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
    	}
   	};
      
   	var promptlangPanel = new Ext.taopix.LangPanel({
		id: 'promptLang', 
		name:'prompt',
		height:150,
        width: 450,
		post:true,
		data: {langList: promptList, dataList: dataList2},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 185, textCol: 202, delCol: 35},
			fieldWidth:  {langField: 175, textField: 186},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
   
   	var promptContainer = {
		xtype: 'panel',
    	width: 450,
       	bodyBorder: false,
        border:false,
        fieldLabel: "{/literal}{#str_LabelPrompt#}{literal}",
        items: promptlangPanel
    };
   
    var pricingModelStore = new Ext.data.ArrayStore({
		id: 'pricingModelStore',
		fields: ['id', 'name'],
		data: [
			{/literal}
			{section name=index loop=$pricingModelData}
			{if $smarty.section.index.last}
				["{$pricingModelData[index].id}", "{$pricingModelData[index].name}"]
			{else}
				["{$pricingModelData[index].id}", "{$pricingModelData[index].name}"],
			{/if}
			{/section}
			{literal}
		]
	});
    
    var pricingModelCombo = new Ext.form.ComboBox({
		id: 'pricingModelCombo',
		name: 'pricingModelCombo',
		width:330,
		fieldLabel: "{/literal}{#str_LabelPricingModel#}{literal}",
		mode: 'local',
		editable: false,
		hideLabel: false,
		forceSelection: true,
		{/literal}{if $hascomponents == 1 || $isprivate == 1}{literal}
			disabled: true,
		{/literal}{else}{literal}
	    	disabled: false,
	    {/literal}{/if}{literal}
		selectOnFocus: true,
		triggerAction: 'all',
		store: pricingModelStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		value: "{/literal}{$categorypricingmodel}{literal}",
		allowBlank: false,
		post: true
	});
    
    var displayTypeStore = new Ext.data.ArrayStore({
		id: 'displayTypeStore',
		fields: ['id', 'name'],
		data: [
			{/literal}
			{section name=index loop=$displayTypeData}
			{if $smarty.section.index.last}
				["{$displayTypeData[index].id}", "{$displayTypeData[index].name}"]
			{else}
				["{$displayTypeData[index].id}", "{$displayTypeData[index].name}"],
			{/if}
			{/section}
			{literal}
		]
	});

	var onlineDisplayStore = new Ext.data.ArrayStore({
		id: 'onlineDisplayStore',
		fields: ['id', 'name'],
		data: [
			{/literal}
			{section name=index loop=$onlineDisplayData}
			{if $smarty.section.index.last}
				["{$onlineDisplayData[index].id}", "{$onlineDisplayData[index].name}"]
			{else}
				["{$onlineDisplayData[index].id}", "{$onlineDisplayData[index].name}"],
			{/if}
			{/section}
			{literal}
		]
	});
    
    var displayTypeCombo = new Ext.form.ComboBox({
		id: 'displayTypeCombo',
		name: 'displayTypeCombo',
		width:110,
		fieldLabel: "{/literal}{#str_LabelDisplayType#}{literal}",
		mode: 'local',
		editable: false,
		{/literal}{if $hascomponents == 1 || $isprivate == 1}{literal}
			disabled: true,
		{/literal}{else}{literal}
	    	disabled: false,
	    {/literal}{/if}{literal}
		hideLabel: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		store: displayTypeStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		value: "{/literal}{$categorydisplaytype}{literal}",
		allowBlank: false,
		post: true
	});

	var onlineComponentDisplayStageProjectStart = new Ext.form.Checkbox({
		id: 'onlinedisplaystageprojectstart',
		name: 'onlinedisplaystageprojectstart',
		boxLabel: "{/literal}{#str_ComponentsDisplayStartingProject#}{literal}",
		{/literal}{if (($onlinedisplaystage & $TPX_COMPONENT_DISPLAY_STAGE_ORDER) === $TPX_COMPONENT_DISPLAY_STAGE_ORDER)}{literal}
		disabled: false,
		{/literal}{else}{literal}
		disabled: true,
		{/literal}{/if}{literal}
		style: 'margin-left: 15px; margin-top: 10px;',
		{/literal}{if (($onlinedisplaystage & $TPX_COMPONENT_DISPLAY_STAGE_START) === $TPX_COMPONENT_DISPLAY_STAGE_START)}{literal}
		checked: true
		{/literal}{else}{literal}
		checked: false
		{/literal}{/if}{literal}
	});

	var onlineComponentDisplayStageOrder = new Ext.form.Checkbox({
		id: 'onlinedisplaystageorder',
		name: 'onlinedisplaystageprojectorder',
		boxLabel: "{/literal}{#str_ComponentsDisplayAddingToBasket#}{literal}",
	    disabled: false,
		listeners:
		{
			check: function(pCheckBox, pChecked)
			{
				var onlineDisplayStageProjectStartCmp = Ext.getCmp('onlinedisplaystageprojectstart');
				onlineDisplayStageProjectStartCmp.setDisabled(! pChecked);
				onlineDisplayStageProjectStartCmp.setValue(false);
			}
		},	
		{/literal}{if $onlinedisplaystage == $TPX_COMPONENT_DISPLAY_STAGE_ORDER || $onlinedisplaystage == $TPX_COMPONENT_DISPLAY_STAGE_ALL}{literal}
		checked: true
		{/literal}{else}{literal}
    	checked: false
    	{/literal}{/if}{literal}
	});
    
    var requiresPageCount = new Ext.form.Checkbox({
		id: 'requirespagecount',
		name: 'requirespagecount',
		boxLabel: "{/literal}{#str_LabelRequiresPageCount#}{literal}",
		{/literal}{if $isprivate == 1}{literal}
			disabled: true,
		{/literal}{else}{literal}
	    	disabled: false,
	    {/literal}{/if}{literal}
		{/literal}{if $requirespagecount == 1}{literal}
		checked: true
		{/literal}{else}{literal}
    	checked: false
    	{/literal}{/if}{literal}
	});
    
	var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel', 
		name:'name',
		height:150,
        width: 450,
		post:true,
		data: {langList: langListStore, dataList: dataList},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 185, textCol: 202, delCol: 35},
			fieldWidth:  {langField: 175, textField: 186},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
	
	var LangContainer = {
        xtype: 'panel',
        width: 450,
        bodyBorder: false,
        border:false,
        fieldLabel: "{/literal}{#str_LabelName#}{literal}",
        items: langPanel
    };
	
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:300,
		{/literal}{if $hascomponents == 1 || $isprivate == 1}{literal}
			disabled: true,
		{/literal}{else}{literal}
	    	disabled: false,
	    {/literal}{/if}{literal}
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		allowBlank:false,
		{/literal}{if $isEdit == 1 || $companyLogin}{literal}
			{/literal}{if $companycode == ""}{literal}
				defvalue: 'GLOBAL',
			{/literal}{else}{literal}
				defvalue: '{/literal}{$companycode}{literal}',
			{/literal}{/if}{literal}
		{/literal}{else}{literal}	
			defvalue: 'GLOBAL',
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
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'componentCategoryForm',
        labelAlign: 'left',
        labelWidth:120,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
        	{ 
        		xtype: 'textfield', 
              	id: 'code', 
              	name: 'code', 
             	allowBlank: false,
              	maxLength: 50,
				width:300,
				{/literal}{if $isEdit == 1}{literal}
                	value: '{/literal}{$categorycode}{literal}', 
                	style: {textTransform: "uppercase", background: "#dee9f6"},
                	readOnly: true,
              	{/literal}{else}{literal}    
					style: {textTransform: "uppercase"},
		      	{/literal}{/if}{literal}
              	fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
              	listeners:{
  					blur:{ fn: forceAlphaNumeric }
  				},
              	post: true,
				validator: function(pValue){
					return (pValue !== 'TAOPIX_RETRO_PRINTS'); 
				}
             },
             {/literal}{if $optionms}{literal}
	             {/literal}{if $showcompany}{literal}
	            	 companyCombo,
	             {/literal}{/if}{literal}
             {/literal}{/if}{literal}
             LangContainer,
             promptContainer,
             { 
         		xtype: 'textfield', 
               	id: 'numberofdeciamalplaces', 
               	name: 'numberofdeciamalplaces', 
              	allowBlank: false,
               	maxLength: 2,
 				width:50,
                value: '{/literal}{$decimalplaces}{literal}', 
                fieldLabel: "{/literal}{#str_LabelDecimalPlaces#}{literal}", 
               	post: true
              },
         {/literal}{if {!in_array($categorycode, ['TAOPIXAI', 'CALENDARCUSTOMISATION', 'SINGLEPRINT', 'SINGLEPRINTOPTION'])}}{literal}
			  {
				
				fieldLabel: "{/literal}{#str_LabelComponentUpsell#}{literal}",
				autoWidth: true,
				items:
				[
          			onlineComponentDisplayStageOrder,
					onlineComponentDisplayStageProjectStart
				],
				style: 'padding-bottom: 5px;',
			 },
       {/literal}{/if}{literal}
             pricingModelCombo,
             displayTypeCombo,
             requiresPageCount,
             { xtype: 'hidden', id: 'originaldisplaytype', name: 'originaldisplaytype', value: "{/literal}{$categorydisplaytype}{literal}",  post: true}	
        ]
    });
	    
    /* create modal window for add and edit */
    var gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight:false,
	  	autoHeight: true,
	  	width: 610,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {   
				fn: function(){
    	componentCategoriesEditWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons', 	
	  	title: "{/literal}{$title}{literal}",
	  	buttons: 
		[
			
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left', 
      			ctCls: 'width_100',
      			{/literal}{if $isActive == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ Ext.getCmp('dialog').close();},
				cls: 'x-btn-right' 		
			},
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				id: 'addEditButton',
				cls: 'x-btn-right', 	
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
		pricingModelCombo.setValue(pricingModelStore.getAt(0).get('id'));  
    {/literal}{/if}{literal}
	gDialogObj.show();	
}

{/literal}