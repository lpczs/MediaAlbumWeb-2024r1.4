{literal}

function initialize(pParams)
{	
	taxZoneEditWindowExists = false;
	
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal} gridDataStoreObj.groupBy('companycode'); {/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}		
	}

	function companyRenderer(value, p, record)
	{
		if (value == '')
		{
			return "<i>{/literal}{#str_Global#}{literal}</i>";
		}
		else
		{
			return value;
		}
	}
	
	function taxLevelRenderer(value, p, record)
	{
		if (value == '')
		{
			return "<i>{/literal}{#str_LabelNone#}{literal}</i>";
		}
		else
		{
			return value;
		}
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
			remoteGroup: true,
		{/literal}{/if}{literal}
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminTaxZones.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([	                        
		    {/literal}{if $optionms}{literal}
		    {name: 'id', mapping: 0},
			{name: 'companycode', mapping: 1},
		    {name: 'code', mapping: 2},
			{name: 'name', mapping: 3},
			{name: 'taxlevel1', mapping: 4},
			{name: 'taxlevel2', mapping: 5},
			{name: 'taxlevel3', mapping: 6},
			{name: 'taxlevel4', mapping: 7},
			{name: 'taxlevel5', mapping: 8},
			{name: 'shiptaxcode', mapping: 9},
			{name: 'countries', mapping: 10}
			{/literal}{else}{literal}
			{name: 'id', mapping: 0},	
		    {name: 'code', mapping: 1},
			{name: 'name', mapping: 2},
			{name: 'taxlevel1', mapping: 3},
			{name: 'taxlevel2', mapping: 4},
			{name: 'taxlevel3', mapping: 5},
			{name: 'taxlevel4', mapping: 6},
			{name: 'taxlevel5', mapping: 7},
			{name: 'shiptaxcode', mapping: 8},
			{name: 'countries', mapping: 9}
			{/literal}{/if}{literal}
			])
		),
		sortInfo: {field: 'code', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 200, renderer: companyRenderer, dataIndex: 'companycode'},			
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, renderer: defaultRenderer, dataIndex: 'code'},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 250, dataIndex: 'name'},
			{header: "{/literal}{#str_LabelTaxCode1#}{literal}", width: 150, dataIndex: 'taxlevel1'},
			{header: "{/literal}{#str_LabelTaxCode2#}{literal}", width: 150, dataIndex: 'taxlevel2', renderer: taxLevelRenderer},
			{header: "{/literal}{#str_LabelTaxCode3#}{literal}", width: 150, dataIndex: 'taxlevel3', renderer: taxLevelRenderer},
			{header: "{/literal}{#str_LabelTaxCode4#}{literal}", width: 150, dataIndex: 'taxlevel4', renderer: taxLevelRenderer},
			{header: "{/literal}{#str_LabelTaxCode5#}{literal}", width: 150, dataIndex: 'taxlevel5', renderer: taxLevelRenderer},
			{header: "{/literal}{#str_LabelShippingTaxCode#}{literal}", width: 150, dataIndex: 'shiptaxcode'},
			{header: "{/literal}{#str_LabelCountries#}{literal}", id: 'countryIndex', width: 200, dataIndex: 'countries'}
		]
	});
	
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
	 	
	 	if(!taxZoneEditWindowExists)
		{
	 		taxZoneEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminTaxZones.addDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
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
				break;
		 	}
	 	}
	 	{/literal}{/if}{literal}
	 	
	 	if(!taxZoneEditWindowExists)
		{
	 		taxZoneEditWindowExists = true;
	 		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminTaxZones.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
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
		
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				codeList = codeList + selRecords[rec].data.code;
			
				if (rec != selRecords.length - 1)
				{
					codeList = codeList + ',';
				}	
			}
		
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
			paramArray['codelist'] = codeList;
		
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminTaxZones.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);	
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
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: icon	});
			}
			dataStore.load();
		}
		gridObj.deleteButton.disable();
	}

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_TitleZones#}{literal}" : "{/literal}{#str_TitleZone#}{literal}"]})' }),
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		ctCls: 'grid',
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
		title: "{/literal}{#str_TaxTitleTaxZones#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
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
