{literal}
function initialize(pParams)
{			    			
	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminSitesOrderRouting.routingAdd&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('routingForm'), form = fp.getForm();
		Ext.taopix.formPanelPost(fp, form, null, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		var submitURL = 'index.php?fsaction=AdminSitesOrderRouting.routingRulesEdit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('routingForm'), form = fp.getForm();
		Ext.taopix.formPanelPost(fp, form, null, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
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
			Ext.MessageBox.show({
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
	}
	
	var ruleStore = new Ext.data.ArrayStore({
			id: 'rulestore',
			fields: ['id', 'name'],
			data: [
					{/literal}
					{section name=index loop=$routingRules}
					{if $smarty.section.index.last}
					
							["{$routingRules[index].id}", "{$routingRules[index].name}"]
					{else}
							["{$routingRules[index].id}", "{$routingRules[index].name}"],
					{/if}
					{/section}
					{literal}
				]
		});
		
		var ruleCombo = new Ext.form.ComboBox({
			id: 'rule',
			name: 'rule',
			width: 380,
			fieldLabel: "{/literal}{#str_LabelCondition#}{literal}",
			mode: 'local',
			editable: false,
			forceSelection: true,
			selectOnFocus: true,
			triggerAction: 'all',
			store: ruleStore,
			listeners:{
				select:{
					fn: function(comboBox, record, index){
						var conditionValueBox = Ext.getCmp('conditionvalue');
						var conditionBox = Ext.getCmp('condition');
						var siteBox = Ext.getCmp('site');
						if(index == 4)
						{
							conditionValueBox.setDisabled(true);
							conditionValueBox.clearValue();
							conditionValueBox.store.removeAll();
							
							conditionBox.setDisabled(true);
							conditionBox.clearValue();
            				
							siteBox.setDisabled(true);
            				siteBox.clearValue();
						}
						else
						{
							conditionValueBox.setDisabled(false);
							conditionBox.setDisabled(false);
							siteBox.setDisabled(false);
					
							conditionValueBox.clearValue();
							conditionValueBox.setValue('');
							conditionValueBox.store.removeAll();
							conditionValueBox.store.reload({
                				params: {
									conditionId: comboBox.getValue(),
									csrf_token: Ext.taopix.getCSRFToken()
								}
            				});
							
							
							Ext.getCmp('conditionvalue').store.on({'load': function(){
								var value = Ext.getCmp('conditionvalue').store.getAt(0);
								Ext.getCmp('conditionvalue').store.getAt(0);
								Ext.getCmp('conditionvalue').setValue(value.data['id']);
								} 
							});	
							
							conditionBox.setValue("{/literal}{#str_LabelIs#}{literal}");
							siteBox.setValue("{/literal}{#str_LabelNone#}{literal}");
						}
					}
				}
			},
			valueField: 'id',
			displayField: 'name',
			useID: true,
			value: "{/literal}{$ruleDefault}{literal}",
			allowBlank: false,
			post: true
		});
				
		var siteStore = new Ext.data.Store({
			id: 'sitestore',
			proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref={/literal}{$ref}{literal}&cmd=PRODSITESCOMBO', method: 'GET'}),
			reader: new Ext.data.ArrayReader({
				idIndex: 0},
			Ext.data.Record.create([
			    {name: 'id', mapping: 2},
				{name: 'name', mapping: 1}
				])
			)
		});
		
		var siteCombo = new Ext.form.ComboBox({
			id: 'site',
			name: 'site',
			width: 380,
			fieldLabel: "{/literal}{#str_LabelSiteCode#}{literal}",
			mode: 'local',
			editable: false,
			forceSelection: true,
			selectOnFocus: true,
			triggerAction: 'all',
			store: siteStore,
			valueField: 'id',
			displayField: 'name',
			useID: true,
			allowBlank: false,
			mode: 'local',
			post: true
		});
		
		var conditionStore = new Ext.data.ArrayStore({
			id: 'conditionstore',
			fields: ['id', 'name'],
			data: [
					{/literal}
					{section name=index loop=$routingConditions}
					{if $smarty.section.index.last}
					
							["{$routingConditions[index].id}", "{$routingConditions[index].name}"]
					{else}
							["{$routingConditions[index].id}", "{$routingConditions[index].name}"],
					{/if}
					{/section}
					{literal}
				]
		});
		
		var conditionCombo = new Ext.form.ComboBox({
			id: 'condition',
			name: 'condition',
			width: 150,
			mode: 'local',
			editable: false,
			hideLabel: false,
			forceSelection: true,
			selectOnFocus: true,
			triggerAction: 'all',
			store: conditionStore,
			valueField: 'id',
			displayField: 'name',
			useID: true,
			value: "{/literal}{$conditionDefault}{literal}",
			allowBlank: false,
			post: true
		});
		
		var valueStore = new Ext.data.Store({
			id: 'valuestore',
			proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminSitesOrderRouting.getConditionValueStore&ref={/literal}{$ref}{literal}'}),
			reader: new Ext.data.ArrayReader({
				idIndex: 0},
			Ext.data.Record.create([
			    {name: 'id', mapping: 0},
				{name: 'name', mapping: 1}
				])
			)
		});
		
		var conditionValueCombo = new Ext.form.ComboBox({
			id: 'conditionvalue',
			name: 'conditionvalue',
			width: 380,
			mode: 'local',
			editable: false,
			minChars:1,
			hideLabel: false,
			forceSelection: true,
			selectOnFocus: true,
			triggerAction: 'all',
			store: valueStore,
			valueField: 'id',
			displayField: 'name',
			useID: true,
			allowBlank: false,
			{/literal}{if $isEdit == 0}{literal}
				emptyText: "{/literal}{$conditionValueDefault}{literal}",
			{/literal}{/if}{literal}
			mode: 'local',
			typeAhead: true,
			post: true
		});
		
		var dialogFormPanelObj = new Ext.FormPanel({
			id: 'routingForm',
	        labelAlign: 'left',
	        labelWidth: 140,
	        autoHeight: true,
	        frame:true,
	        bodyStyle:'padding:10px 5px 10px',
	        items: [
				ruleCombo,
                { xtype:'panel', layout: 'form', items: conditionCombo, labelWidth:140},
				conditionValueCombo,
				{ xtype:'panel', layout: 'form', style:'', items: siteCombo}
				
	        ]
	    });
	    
	    /* create modal window for add and edit */
		gDialogObj = new Ext.Window({
	  		id: 'dialog',
	  		title: "{/literal}{$title}{literal}",
	  		closable:false,
	  		plain:true,
	  		modal:true,
	  		draggable:true,
	  		resizable:false,
	  		layout: 'fit',
	  		autoHeight:true,
	  		width: 580,
	  		items: dialogFormPanelObj,
	  		listeners: {
				'close': {   
					fn: function(){
						orderRoutingEditWindowExists = false;
					}
				}
			},
	  		buttons: 
			[
				{
					text: "{/literal}{#str_ButtonCancel#}{literal}",
					handler: function(){ gDialogObj.close();	}
				},
				{
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

	
		var loadValueBox = Ext.getCmp('conditionvalue');
		var ruleBox = Ext.getCmp('rule');
	
		loadValueBox.store.reload({
			params: {
				conditionId: ruleBox.getValue(),
				csrf_token: Ext.taopix.getCSRFToken()
			}
		});
	
		if (ruleBox.getValue() == 4)
		{
			var emptyConditionBox = Ext.getCmp('condition');
			var emptyConditionValueBox = Ext.getCmp('conditionvalue');
			var emptySiteBox = Ext.getCmp('site');

			emptyConditionBox.setDisabled(true);
			emptyConditionValueBox.setDisabled(true);
			emptyConditionBox.reset();
			emptySiteBox.setDisabled(true);
			emptySiteBox.reset();
		}
	
		Ext.getCmp('site').store.reload();
		Ext.getCmp('conditionvalue').store.reload();
			
		Ext.getCmp('site').store.on({'load': function(){
				Ext.getCmp('site').setValue("{/literal}{$siteDefault}{literal}")
			} 
		});	
				
		{/literal}{if $isEdit == 1}{literal}
			Ext.getCmp('conditionvalue').store.on({'load': function(){
				Ext.getCmp('conditionvalue').setValue("{/literal}{$conditionValueDefault}{literal}");
				} 
			});	
		{/literal}{/if}{literal}
		gDialogObj.show();	  
}

{/literal}
