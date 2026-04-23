{literal}

var previousReason = '';

function initialize(pParams)
{	
	function enableReason()
	{
		var onhold = Ext.getCmp('onhold').getValue();
		var reason = Ext.getCmp('reason');

		if (onhold)
		{
			if (reason.getValue() == '')
			{
				reason.setValue(previousReason);
			}
			reason.enable();
		} 
		else
		{
			previousReason = reason.getValue();
			reason.setValue('');
			reason.disable();
		}
	}

 	var okFunction = function(btn,ev)
 	{
		var fp = Ext.getCmp('onHoldForm');
		var form = fp.getForm();
		var gridObj = Ext.getCmp('productionmaingrid');
		var idlist = Ext.taopix.gridSelection2IDList(gridObj);

		var params = [];
		params['idlist'] = idlist;
		params['ref'] = '{/literal}{$ref}{literal}';
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateItemOnHoldStatus', "{/literal}{#str_MessageSaving#}{literal}", onHoldCallback);
	};
	
	var cancelFunction = function(btn,ev)
	{ 
		onHoldWindowExists = false; 
		gDialogObj.close();
	};

	var orderDetailsFormTabPanelObj = new Ext.FormPanel({
        id: 'onHoldForm',
		header: false,
        frame: true,
        layout: 'form',
		autoWidth: true,
		bodyBorder: false,
		border: true,
		labelWidth: 130,
        items: [ 
			new Ext.form.ComboBox({
				id: 'onhold',
				name: 'onhold',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "{/literal}{#str_LabelProductionOnHold#}{literal}",
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data:
					[
						[0, "{/literal}{#str_LabelNo#}{literal}"],
						[1, "{/literal}{#str_LabelYes#}{literal}"]
					]
				}),
				valueField: 'id',
				value: 0,
				displayField: 'name',
				width: 130,
				useID: true,
				post: true,
				listeners: { 'select': function(){enableReason();} }
			}),
			{
				xtype: 'textfield',
				id: 'reason',
				name: 'reason',
				fieldLabel: "{/literal}{#str_LabelProductionOnHoldReason#}{literal}",
				width: 350,
				maxLength: 512,
				post: true,
				disabled: true
			}
		]
	});

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		title: "{/literal}{#str_ButtonHold#}{literal}",
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 150,
		width: 550,
		items: orderDetailsFormTabPanelObj,
		buttons:
		[
			{ text: '{/literal}{#str_ButtonCancel#}{literal}', handler: cancelFunction},
			{ text: "{/literal}{#str_ButtonOk#}{literal}", handler: okFunction, cls: 'x-btn-right' }
		]
	});

	var mainPanel = Ext.getCmp('dialog');
	mainPanel.show();	
}

{/literal}