{literal}
function initialize(pParams)
{	
	var editSaveHandler = function()
	{
		if((Ext.getCmp('mainform').getForm().isValid()) && (Ext.getCmp('code').isValid()))
		{
			var parameter = [];
			
			if (!Ext.getCmp('langPanel').isValid())
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING});
				return false;
			}	
			
    		if (Ext.getCmp('isactive').checked)
			{
				parameter['active'] = '1';
			}
			else
			{
				parameter['active'] = '0';
			}
			
			var intervalTypeObj = Ext.getCmp('intervalType');
			if ((intervalTypeObj.getValue() == '1') || (intervalTypeObj.getValue() == '3'))
			{
				parameter['intervalValue'] = Ext.getCmp('intervalValueNumber').getValue();
			}
			else
			{
				parameter['intervalValue'] = Ext.getCmp('intervalValueTime').getValue();
			}
		
			var fp = Ext.getCmp('mainform'), form = fp.getForm();
 		   	parameter['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('tasksGrid'));
		   	{/literal}{if $isEdit == 0}{literal}
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminScheduledTasks.taskAdd', "{/literal}{#str_MessageSaving#}{literal}", onCallback); 
    		{/literal}{else}{literal}
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminScheduledTasks.taskEdit', "{/literal}{#str_MessageSaving#}{literal}", onCallback); 
    		{/literal}{/if}{literal}
    	}
    	else
    	{
			return false;
		}
	};
	
	var setIntervalValue = function()
	{
		var intervalTypeObj = Ext.getCmp('intervalType');

		if ((intervalTypeObj.getValue() == '1') || (intervalTypeObj.getValue() == '3'))
		{
			Ext.getCmp('intervalValueNumber').allowBlank = false;		
			Ext.getCmp('intervalValueNumber').show();
							
			Ext.getCmp('intervalValueTime').allowBlank = true;			
			Ext.getCmp('intervalValueTime').hide();			
		}
		else
		{
			Ext.getCmp('intervalValueNumber').allowBlank = true;			
			Ext.getCmp('intervalValueNumber').hide();
			
			Ext.getCmp('intervalValueTime').allowBlank = false;
			Ext.getCmp('intervalValueTime').show();			
		}
	};
	
	
	var topPanel = new Ext.Panel({ 
		id: 'topPanel', 
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf; margin-bottom: 2px', 
		plain:true, 
		bodyBorder: false, 
		border: false, 
		defaults: {xtype: 'textfield', labelWidth: 60, width: 300},  
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: [
			{ 
				xtype: 'container', width: 700,
				layout: 'column', 
				items:
				[
					{
						xtype: 'container', width: 300,
						layout: 'form', defaults: {xtype: 'textfield', labelWidth: 60, width: 200},  
						items: 
						[
							{ 
								xtype: 'textfield',
								id: 'code', 
								name: 'code',
								fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
								validateOnBlur:true,
								maskRe: /^\w+$/,
								post: true, 
								allowBlank: false, 
								maxLength: 50, 
								{/literal}{if $isEdit == 0}{literal}
									readOnly: false,
									style: {textTransform: "uppercase"}
								{/literal}{else}{literal}
									value: "{/literal}{$taskCode}{literal}",
									readOnly: true,
									style: 'background:#c9d8ed; textTransform: uppercase'	
								{/literal}{/if}{literal} 
							}
						]
					},
					
					{
						xtype: 'container', width: 290,
						layout: 'form', defaults: {xtype: 'textfield', labelWidth: 60, width: 210},  
						items:
						[
							{/literal}{if $isEdit == 1}{literal}
								{ 
									xtype: 'textfield', 
									name: 'scriptName', 
									id: 'scriptName', 
									width: 200, 
									labelStyle: 'width:75px',
									fieldLabel: "{/literal}{#str_LabelScript#}{literal}", 
									value: "{/literal}{$scriptFileName}{literal}",
									readOnly: true,
									style: 'background:#c9d8ed;'	
								}
							{/literal}{else}{literal}
								{
									xtype: 'combo',
									id: 'scriptName',
									name: 'scriptName',
									mode: 'local',
									editable: false,
									forceSelection: true,
									selectOnFocus: true,
									triggerAction: 'all',
									fieldLabel: "{/literal}{#str_LabelScript#}{literal}",
									width: 200,
									labelStyle: 'width:75px',
									store: new Ext.data.ArrayStore({
										id: 0,
										fields: ['id', 'name'],
										data: [
											{/literal}
											{section name=index loop=$sciptsList}
												{if $smarty.section.index.last}
													["{$sciptsList[index]}", "{$sciptsList[index]}"]
												{else}
													["{$sciptsList[index]}", "{$sciptsList[index]}"],
												{/if}
											{/section}
											{literal}
										]
									}),
									listeners: 
									{ 
										'select': function()
										{
											var conn = new Ext.data.Connection();
								
											conn.request({
		    									url: './?fsaction=AdminScheduledTasks.getScriptInfo&ref='+sessionId+'&scriptName='+escape(this.getValue()),
		    									method: 'GET',
		    									params: {},
		    									success: function(responseObject) 
		    									{
		    										var taskArray = eval('(' + responseObject.responseText + ')');
													if (taskArray instanceof Array)
													{
														var taskCode = taskArray[0];
														var taskIntervalType = taskArray[1];
														var taskIntervalValue = taskArray[2];
														gLocalizedCodesArray = eval('(' + taskArray[3] +')');
														gLocalizedNamesArray = eval('(' + taskArray[4] +')');
														var maxRunCount = taskArray[5];
														var deleteExpiredInterval = taskArray[6];

    													Ext.getCmp('code').setValue(taskCode);
    													Ext.getCmp('intervalType').setValue(taskIntervalType);
    													
    													if ((taskIntervalType == '1') || (taskIntervalType == '3'))
														{
															Ext.getCmp('intervalValueNumber').setValue(taskIntervalValue);
														}
														else
														{
															Ext.getCmp('intervalValueTime').setValue(taskIntervalValue);
														}
    													setIntervalValue();
    													
    													var languageGridData = getLanguageGridData();
    													var newData = {langList: languageGridData['langListStore'], dataList: languageGridData['dataList']};
    													Ext.getCmp('langPanel').loadData(newData);
    													
    													Ext.getCmp('maxRunCount').setValue(maxRunCount);
    													Ext.getCmp('deleteExpiredInterval').setValue(deleteExpiredInterval);
    												}
													else
													{
														Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: taskArray, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING});
														return false;
													}
												},
		    									failure: function() { alert('error'); }
											});

										} 
									},
									valueField: 'id',
									displayField: 'name',
									useID: true,
									allowBlank: false,
									validateOnBlur:true, 
									{/literal}{if $isEdit == 1}{literal}
									value: "{/literal}{$scriptFileName}{literal}",
									{/literal}{/if}{literal} 
									post: true
								}
							{/literal}{/if}{literal} 
						]
					}
				]
			}
		]
	});	
	
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';
	
	{/literal}{$localizedcodesjavascript}{literal}
	{/literal}{$localizednamesjavascript}{literal}
	{/literal}{$languagecodesjavascript}{literal}
	{/literal}{$languagenamesjavascript}{literal}
		
	var getLanguageGridData = function()
	{
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
    	return {'langListStore': langListStore, 'dataList': dataList};
	};
	
	var languageGridData = getLanguageGridData();
	
        
	var langContainer = {
        xtype: 'panel',
        width: 464,
        bodyBorder: false,
        border:false,
    	fieldLabel: "{/literal}{#str_LabelName#}{literal}",
        items: 
        [
       		new Ext.taopix.LangPanel({
				id: 'langPanel', 
				name:'name',
				height:150,
				post:true,
				data: {langList: languageGridData['langListStore'], dataList: languageGridData['dataList']},
				settings: 
				{ 
					headers:     {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
					defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
					columnWidth: {langCol: 200, textCol: 201, delCol: 35},
                    fieldWidth:  {langField: 185, textField: 187},
					errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
				}
			})    
        ]
    };
	
	
	var mainPanel = new Ext.Panel({ 
		id: 'mainPanel', 
		layout: 'form',
		defaults: {xtype: 'textfield', labelWidth: 120, width: 300},  
		frame: true,
		bodyStyle:'padding: 2px 5px 5px 5px;',
		items: 
		[
			langContainer,
			
			new Ext.form.ComboBox({
				id: 'intervalType',
				name: 'intervalType',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "{/literal}{#str_LabelIntervalType#}{literal}",
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data: [
						{/literal}
						{section name=index loop=$intervalTypes}
							{if $smarty.section.index.last}
								["{$intervalTypes[index].id}", "{$intervalTypes[index].name}"]
							{else}
								["{$intervalTypes[index].id}", "{$intervalTypes[index].name}"],
							{/if}
						{/section}
						{literal}
					]
				}),
				listeners: { 
					'select': setIntervalValue
				},
				valueField: 'id',
				displayField: 'name',
				useID: true,
				value: "{/literal}{$intervalType}{literal}",
				post: true
			}),
			{ 
                xtype: 'numberfield', 
                name: 'intervalValueNumber', 
                allowBlank: false, 
                id: 'intervalValueNumber', 
                fieldLabel: "{/literal}{#str_LabelIntervalValue#}{literal}", 
                width: 100, 
                allowNegative: false, 
                decimalPrecision: 0, 
                allowDecimals: false, 
                minValue: 1
            },
			{ 
                xtype: 'textfield', 
                name: 'intervalValueTime', 
                id: 'intervalValueTime', 
                width: 100, 
                fieldLabel:"{/literal}{#str_LabelIntervalValue#}{literal}", 
                allowBlank: false 
            }, 
			{ 
                xtype: 'numberfield', 
                id: 'maxRunCount', 
                name: 'maxRunCount', 
                {/literal}
                {if $isEdit == 1}
                    {literal}
                value: "{/literal}{$maxRunCount}{literal}", 
                    {/literal}
                {/if}
                {literal}     
                allowBlank: false, 
                post: true, 
                width: 100, 
                fieldLabel:  "{/literal}{#str_LabelMaxRunCount#}{literal}", 
                allowNegative: false, 
                decimalPrecision: 0, 
                allowDecimals: false
            },
			{ 
                xtype: 'numberfield', 
                name: 'deleteExpiredInterval', 
                {/literal}
                {if $isEdit == 1}
                    {literal}
                value: "{/literal}{$deleteExpiredInterval}{literal}", 
                    {/literal}
                {/if}
                {literal}  
                post: true, 
                allowBlank: false, 
                id: 'deleteExpiredInterval', 
                width: 100, 
                fieldLabel: "{/literal}{#str_LabelDeleteEventsAfterDays#}{literal}", 
                allowNegative: false, 
                decimalPrecision: 0, 
                allowDecimals: false
            }
		]
	});	
	
	
	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 620,
		autoHeight: true,
		layout: 'form',
		defaultType: 'textfield',
		items: [ topPanel, mainPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});
	
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		title: "{/literal}{$title}{literal}",
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 620,
		height: 450,
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
			{	
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogObj.close(); scheduledTasksEditWindowExists = false;},
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				id: 'updateButton',
				cls: 'x-btn-right',
				{/literal}{if $isEdit == 0}{literal}
					handler: editSaveHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: editSaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	gDialogObj.show();	  
	
	Ext.getCmp('isactive').setValue("{/literal}{$active}{literal}" == '1' ? true : false);
	Ext.getCmp('intervalValueNumber').setValue("{/literal}{$intervalValue}{literal}");
	Ext.getCmp('intervalValueTime').setValue("{/literal}{$intervalValueTime}{literal}");
	
	setIntervalValue();
	
}
{/literal}