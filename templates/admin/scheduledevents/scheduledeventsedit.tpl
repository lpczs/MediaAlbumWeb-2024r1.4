{literal}
function initialize(pParams)
{	
	var editSaveHandler = function()
	{
		if(Ext.getCmp('mainform').getForm().isValid()) 
		{
			var parameter = [];
			
			if (Ext.getCmp('isactive').checked)
			{
				parameter['active'] = '1';
			}
			else
			{
				parameter['active'] = '0';
			}
			
			var fp = Ext.getCmp('mainform'), form = fp.getForm();
 		   	parameter['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('eventsGrid'));
		   	Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminScheduledEvents.eventEdit', "{/literal}{#str_MessageSaving#}{literal}", onCallback); 
    	}
    	else
    	{
			return false;
		}
	};
	
	
	var topPanel = new Ext.Panel({ 
		id: 'topPanel', 
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf; margin-bottom: 2px', 
		plain:true, 
		bodyBorder: false, 
		border: false, 
		defaults: {xtype: 'textfield', labelWidth: 60, width: 250},  
		bodyStyle:'padding:5px 5px 0 8px; border-top: 0px',
		items: 
		[
			{ 
				xtype: 'textfield',
				id: 'code', 
				name: 'code',
				fieldLabel: "{/literal}{#str_LabelTaskCode#}{literal}", 
				validateOnBlur:true, 
				post: true, 
				allowBlank: false, 
				maxLength: 50, 
				value: "{/literal}{$taskcode}{literal}",
				readOnly: true,
				style: 'background:#c9d8ed; textTransform: uppercase'
			}	
		]
	});	
	    
        
	var mainPanel = new Ext.Panel({ 
		id: 'mainPanel', 
		layout: 'form',
		defaults: {xtype: 'textfield', labelWidth: 120, width: 300},  
		frame: true,
		bodyStyle:'padding: 2px 5px 5px 5px;',
		items: 
		[
			{
				xtype: 'combo',
				id: 'priority',
				name: 'priority',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "{/literal}{#str_LabelPriority#}{literal}",
				width: 250,
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data: [
						{/literal}
						{section name=index loop=$priorityList}
							{if $smarty.section.index.last}
								["{$priorityList[index].id}", "{$priorityList[index].name}"]
							{else}
								["{$priorityList[index].id}", "{$priorityList[index].name}"],
							{/if}
						{/section}
						{literal}
					]
				}),
				valueField: 'id',
				displayField: 'name',
				useID: true,
				allowBlank: false,
				validateOnBlur:true, 
				value: "{/literal}{$priority}{literal}",
				post: true
			},
		
			{ xtype: 'numberfield', name: 'maxruncount', id: 'maxruncount', value: "{/literal}{$maxruncount}{literal}", width: 100, fieldLabel: "{/literal}{#str_EventMaxRunCount#}{literal}",  disabled: true, allowNegative: false}, 
			{ xtype: 'numberfield', name: 'runcount', id: 'runcount', value: "{/literal}{$runcount}{literal}", width: 100, fieldLabel: "{/literal}{#str_EventRunCount#}{literal}", allowBlank: false, post: true, allowNegative: false, decimalPrecision: 0, allowDecimals: false}
		]
	});	
	
	
	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 410,
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
		width: 410,
		height: 235,
		items: dialogFormPanelObj,
		listeners: {
			'close': {   
				fn: function(){
					scheduledEventsWindowExists = false;
				}
			}
		},
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
				handler: function(btn, ev){ gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				id: 'updateButton',
				cls: 'x-btn-right',
				handler: editSaveHandler
			}
		]
	});

	gDialogObj.show();	  
	
	Ext.getCmp('isactive').setValue("{/literal}{$active}{literal}" == '1' ? true : false);
}
{/literal}