{literal}

function initialize(pParams)
{
	shippingZonesEditWindowExists = false;
	
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycode');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}		
	}

	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycode');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}		
	}

	function companyRenderer(value, p, record)
	{
		{/literal}{if $optionms}{literal}
		if (value == '')
		{
			return "<i>{/literal}{#str_Global#}{literal}</i>";
		}
		else
		{
			return value;
		}
		{/literal}{else}{literal}
			return value;
		{/literal}{/if}{literal}
	}
	
	function defaultRenderer(value, p, record)
	{
		if (value == '')
		{
			return "<i>{/literal}{#str_LabelDefault#}{literal}</i>";
		}
		else
		{
			return value;
		}
	}

	/* add handler */
	function onAdd(btn, ev)
	{		
		var serverParams = new Object();
	 	serverParams['companyHasROW'] = 0;
	 
	 	var dataStore = Ext.getCmp('maingrid').store;
	 	{/literal}{if $optionms}{literal}
	 	for (var i=0; i < dataStore.data.items.length; i++)
	 	{
			if (dataStore.data.items[i].data.code == '' && dataStore.data.items[i].data.companycode == '{/literal}{$companycode}{literal}')
		 	{
				serverParams['companyHasROW'] = 1;
		 	}
	 	}
	 	{/literal}{/if}{literal}
	 	
	 	if(!shippingZonesEditWindowExists)
	 	{
	 		shippingZonesEditWindowExists = true;
	 		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminShippingZones.addDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
	 	}
	}	
	
	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['companyHasROW'] = 0;
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		serverParams['id'] = id;
	
		var dataStore = Ext.getCmp('maingrid').store;
	 
		{/literal}{if $optionms}{literal}
	 	for (var i=0; i < dataStore.data.items.length; i++)
	 	{
			if (dataStore.data.items[i].data.code == '' && dataStore.data.items[i].data.companycode == '{/literal}{$companycode}{literal}')
		 	{
				serverParams['companyHasROW'] = 1;
		 	}
	 	}
	 	{/literal}{/if}{literal}
	 	
	 	if(!shippingZonesEditWindowExists)
	 	{
	 		shippingZonesEditWindowExists = true;
	 		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminShippingZones.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
	 	}
	 	
	}

	/* delete handler */	  
	function onDelete(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('maingrid');
		var dataStore = gridObj.store;
		var selRecords = gridObj.selModel.getSelections();
		var codeList = '';
	
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			if (selRecords[rec].data.code == '')
			{
				codeList = codeList + "{/literal}{#str_LabelDefault#}{literal}";
			}
		
			codeList = codeList + selRecords[rec].data.code;
		
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}	
		}
	
		var message = nlToBr("{/literal}{#str_DeleteConfirmation#}{literal}");
		message = message.replace("^0", codeList);

		dataStore.load();
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
			var gridObj = gMainWindowObj.findById('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var codeList = '';
			var companyCodeList = '';
		
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				codeList = codeList + selRecords[rec].data.code;
				if (rec != selRecords.length - 1)
				{
					codeList = codeList + ',';
				}	
			}
		
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				companyCodeList = companyCodeList + selRecords[rec].data.companycode;
				if (rec != selRecords.length - 1)
				{
					companyCodeList = companyCodeList + ',';
				}	
			}
		
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
			paramArray['codelist'] = codeList;
			paramArray['companycodelist'] = companyCodeList;
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminShippingZones.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);	
		}
	}

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			var selRecords = gridObj.getSelectionModel().getSelections();
			var icon = Ext.MessageBox.INFO;
			var idList = ',' + pActionData.result.idlist + ',';

			for (rec = 0; rec < selRecords.length; rec++)
			{
				/* only delete from selection what has been deleted */
				if (idList.indexOf(',' + selRecords[rec]['id'] + ',') !=-1)
				{
					dataStore.remove(selRecords[rec]);
				}
			}
		
			if (pActionData.result.msg)
			{	
				Ext.MessageBox.show({
					title: pActionData.result.title,
					msg: pActionData.result.msg,
					buttons: Ext.MessageBox.OK,
					icon: icon
				});
			}
			dataStore.load();
		}
		gridObj.deleteButton.disable();
	}
		
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					grid.editButton.enable();
				}
				else
				{
					grid.editButton.disable();
					grid.deleteButton.disable();
				}
				
				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
				}
				
				if (selectionCount == 1 || selectionCount > 1)
				{
					var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
					var idList = selectID.split(',');
					
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('maingrid').store.getById(idList[i]);
						
						{/literal}{if $optionms}{literal}
								{/literal}{if $companyLogin}{literal}
									if (record.data['companycode'] == '' && idList[i] > 0)
									{
										grid.editButton.disable();
										grid.deleteButton.disable();
										break;
									}
									else
									{
										if(selectionCount == 1)
										{
											grid.editButton.enable();
										}
										grid.deleteButton.enable();
									}
								{/literal}{else}{literal}
									if (record.data['code'] == '' && record.data['companycode'] == '' && idList[i] > 0)
									{
										grid.deleteButton.disable();
										break;
									}
									else
									{
										grid.deleteButton.enable();
									}
							{/literal}{/if}{literal}
						{/literal}{else}{literal}
							if (record.data['code'] == '' && idList[i] > 0)
							{
								grid.deleteButton.disable();
								break;
							}
							else
							{
								grid.deleteButton.enable();
							}
					 {/literal}{/if}{literal}
					}
				}
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		{/literal}{if $optionms}{literal}
			groupField: 'companycode',
		{/literal}{/if}{literal}
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminShippingZones.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([	                        
		{/literal}{if $optionms}{literal}	
		    {name: 'id', mapping: 0},
			{name: 'companycode', mapping: 1},
			{name: 'code', mapping: 2},
			{name: 'name', mapping: 3},
			{name: 'countrycodes', mapping: 4}
		{/literal}{else}{literal}	
			{name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'name', mapping: 2},
			{name: 'countrycodes', mapping: 3}
		{/literal}{/if}{literal}		
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: true, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 250, renderer: companyRenderer, dataIndex: 'companycode'},			
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, renderer: defaultRenderer, dataIndex: 'code'},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 250, dataIndex: 'name'},
			{header: "{/literal}{#str_LabelCountries#}{literal}", width: 250, dataIndex: 'countrycodes'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		{/literal}{if $optionms}{literal}
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_TitleZones#}{literal}" : "{/literal}{#str_TitleZone#}{literal}"]})' }),
		{/literal}{/if}{literal}
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		ctCls: 'grid',
		stripeRows:true,
		columnLines:true,
		{/literal}{if $optionms}{literal}
			autoExpandColumn: 4,
		{/literal}{else}{literal}
			autoExpandColumn: 3,
		{/literal}{/if}{literal}
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAdd
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			}, '-'
			{/literal}{if $optionms}{literal}
			,
			new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			{/literal}{/if}{literal}
			]
	});
	
	gridDataStoreObj.load();
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_ShippingTitleShippingZones#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}w' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
}
	

/* close this window panel */
function windowClose()
{
	if (Ext.getCmp('MainWindow'))
	{
		centreRegion.remove('MainWindow', true);
		centreRegion.doLayout();
	}
}

{/literal}
