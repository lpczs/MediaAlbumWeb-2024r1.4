{literal}

function initialize(pParams)
{
	shippingRatesEditWindowExists = false;
	
	function clearGrouping(v)
	{	 	
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycodeHidden');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}		
	}
	
	function logicColumnRenderer(value, p, record)
	{
		if (value == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<div '+className+">{#str_LabelInactive#}</div>{literal}";
		}
		else
		{
			return "{/literal}<div>{#str_LabelActive#}</div>{literal}";
		}	
	}

	function generalColumnRenderer(value, p, record)
	{
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+'>'+value+'</span>{literal}';
		}
		else
		{
			return '{/literal}<span class="">'+value+'</span>{literal}';
		}	
	}

	function companyRenderer(value, p, record)
	{
		if (value == '')
		{
			value = "<i>{/literal}{#str_Global#}{literal}</i>";
		}
	
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+'>'+value+'</span>{literal}';
		}
		else
		{
			return '{/literal}<span class="">'+value+'</span>{literal}';
		}	
	}

	function companyCodeRenderer(value, p, record)
	{
		{/literal}{if $optionms}{literal}
		if (value == '')
		{
			return "{/literal}{#str_Global#}{literal}";
		}
		else
		{
			return value;
		}
		{/literal}{else}{literal}
			return value;
		{/literal}{/if}{literal}
	}


	function onActivate(btn, ev)
	{ 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));	
		var active = 0; 
		switch (btn.id)
		{
			case 'activeButton':
				active = 1;
				break;
			case 'inactiveButton':
				active = 0;
				break;
		}	
		serverParams['active'] = active;
		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminShippingRates.shippingRateActivate', "{/literal}{#str_MessageUpdating#}{literal}", activateCallback);
	}

	function activateCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			dataStore.reload();
		}
	}

	/* add handler */
	function onAdd(btn, ev)
	{		
		if (!shippingRatesEditWindowExists)
		{
			shippingRatesEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminShippingRates.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}	
	
	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		serverParams['id'] = id;
		
		if (!shippingRatesEditWindowExists)
		{
			shippingRatesEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminShippingRates.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
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
			codeList = codeList + selRecords[rec].data.code;
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}	
		}
		var message = nlToBr("{/literal}{#str_DeleteConfirmation#}{literal}");
		message = message.replace("^0", codeList);

		dataStore.reload();
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
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminShippingRates.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);	
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
			dataStore.reload();
		}
	}

	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('maingrid').store;
		var hideInactive = 0;

		if (btn.pressed)
		{
			// set to true and update tooltip
			hideInactive = 1;
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');			
		}

		// manually set the last options to allow reload to be passed hide inactive
		gridDataStore.lastOptions.params['hideInactive'] = hideInactive;

		gridDataStore.reload({params: gridDataStore.lastOptions.params});
	}

	function checkHideInactiveButton (pStore, pOptions)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveButton');

		// on detected query turn button off
		if ((pStore.baseParams.query != '') && (typeof pStore.baseParams.query != 'undefined'))
		{
			hideInactiveButton.toggle(false);
			hideInactiveButton.disable();
			hideInactiveButton.setTooltip({text: '{/literal}{#str_TooltipHideInactiveItemsIsDisabledForSearchResults#}{literal}', autoHide: true, id: 'hideInactiveDisabledTooltip'});
		}
		else
		{
			// search field has been emptied for the first time since it was last filled
			if (hideInactiveButton.disabled == true)
			{
				hideInactiveButton.enable();
				var gridDataStore = Ext.getCmp('maingrid').store;

				// restore button to its state before the search 
				if (gridDataStore.lastOptions.params['hideInactive'] == 1)
				{
					hideInactiveButton.toggle(true);
					hideInactiveButton.setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');					
				}
				else
				{
					hideInactiveButton.toggle(false);
					hideInactiveButton.setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');					
				}
			}
		}
	}

	function carryHideInactiveIntoPagingToolbarRefresh(pToolbar, pParams)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveButton');

		if ((typeof hideInactiveButton.pressed != 'undefined') && (hideInactiveButton.pressed == true))
		{
			pParams.hideInactive = 1;
		}
		else
		{
			pParams.hideInactive = 0;
		}
	}
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
		selectionchange: function(gridCheckBoxSelectionModelObj) 
		{
			var selectionCount = gridCheckBoxSelectionModelObj.getCount();
			
			if (selectionCount == 0)
			{
				grid.deleteButton.disable();
			}
			
			if (selectionCount == 1)
			{
				grid.editButton.enable();
			}
			else
			{
				grid.editButton.disable();
			}
			
			if (selectionCount > 0)
			{
				grid.activeButton.enable();
				grid.inactiveButton.enable();
			}
			else
			{
				grid.activeButton.disable();
				grid.inactiveButton.disable();
			}
			
			if (grid)
			{
				var selRecords = grid.getSelectionModel().getSelections();
			}
			
			if (selectionCount > 0)
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
									grid.activeButton.disable();
									grid.inactiveButton.disable();
									break;
								}
								else
								{
									if(selectionCount == 1)
									{
										grid.editButton.enable();
									}
									
									if(selectionCount > 0)
									{
										grid.deleteButton.enable();
										grid.activeButton.enable();
										grid.inactiveButton.enable();
									}
								}
							{/literal}{else}{literal}
								if (record.data['code'] == '' && record.data['companycode'] == '' && idList[i] > 0)
								{
									grid.deleteButton.disable();
									break;
								}
								else
								{
									if(selectionCount > 0)
									{
										grid.deleteButton.enable();
									}
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
							if(selectionCount > 0)
							{
								grid.deleteButton.enable();
							}
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
			groupField: 'companycodeHidden',
		{/literal}{/if}{literal}
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminShippingRates.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([	                        
	    {/literal}{if $optionms}{literal}	
		    {name: 'id', mapping: 0},
			{name: 'companycode', mapping: 1},
			{name: 'code', mapping: 2},
			{name: 'shippingmethodcode', mapping: 3},
			{name: 'shippingzonecode', mapping: 4},
			{name: 'product', mapping: 5},
			{name: 'licensekey', mapping: 6},
			{name: 'additionalinfo', mapping: 7},
			{name: 'rates', mapping: 8},
			{name: 'active', mapping: 9},
			{name: 'companycodeHidden', mapping: 1}
		{/literal}{else}{literal}
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'shippingmethodcode', mapping: 2},
			{name: 'shippingzonecode', mapping: 3},
			{name: 'product', mapping: 4},
			{name: 'licensekey', mapping: 5},
			{name: 'additionalinfo', mapping: 6},
			{name: 'rates', mapping: 7},
			{name: 'active', mapping: 8}
		{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		listeners: { beforeload: checkHideInactiveButton },
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
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 170, dataIndex: 'companycode', renderer: companyRenderer},			
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', menuDisabled:true, renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelShippingMethod#}{literal}", width: 200, dataIndex: 'shippingmethodcode', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelShippingZone#}{literal}", width: 200, dataIndex: 'shippingzonecode', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelProduct#}{literal}", width: 200, dataIndex: 'product', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelLicenseKey#}{literal}", width: 200, dataIndex: 'licensekey', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelAdditionalInformation#}{literal}", width: 200, dataIndex: 'additionalinfo', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelShippingRates#}{literal}", width: 200, dataIndex: 'rates', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: logicColumnRenderer, width: 75, dataIndex: 'active'}
			{/literal}{if $optionms}{literal}	
			,{header: "{/literal}{#str_LabelCompany#}{literal}", width: 170, dataIndex: 'companycodeHidden', hidden:true, renderer: companyCodeRenderer}
			{/literal}{/if}{literal}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelRates#}{literal}" : "{/literal}{#str_LabelRate#}{literal}"]})' }),
		{/literal}{/if}{literal}
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		ctCls: 'grid',
		plugins: [new Ext.ux.grid.Search({
			iconCls: 'silk-zoom',
			minChars: 3,
			width: 200,
			disableIndexes:['active','rates','companycode', 'companycodeHidden','product','licensekey','additionalinfo'],
			autoFocus: true
		})],
		{/literal}{if $optionms}{literal}
			autoExpandColumn: 9,
		{/literal}{else}{literal}
			autoExpandColumn: 6,
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
			}, '-',
			{ 
				id:'activeButton',
				ref: '../activeButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'inactiveButton', 
				ref: '../inactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			},'-',
			{/literal}{if $optionms}{literal}
				new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }}),
			{/literal}{/if}{literal}
			{xtype:'tbfill'},
			{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
			}
			]
			, bbar: new Ext.PagingToolbar({ pageSize: 100, store: gridDataStoreObj, displayInfo: true, listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh }})
	});
	
	gridDataStoreObj.load({params: {hideInactive: 0}});
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_ShippingTitleShippingRates#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
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
