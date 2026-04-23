{literal}
function statusRenderer(value, p, record)
{
	if (value == 0)
	{
		className =  'class = "inactive"';
		return '{/literal}<span '+className+">{#str_LabelInactive#}</span>{literal}";
	}
	else
	{
		return "{/literal}{#str_LabelActive#}{literal}";
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

function generalColumnRenderer(value, p, record)
{
	if (record.data.status == 0)
	{
		className =  'class = "inactive"';
		return '{/literal}<span '+className+'>'+value+'</span>{literal}';
	}
	else
	{
		return '{/literal}<span class="">'+value+'</span>{literal}';
	}	
}

function initialize(pParams)
{	
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
	
	componentCategoriesEditWindowExists = false;
	componentsWindowExists = false;
	
	/* add handler */
	function onAdd(btn, ev)
	{	
		if(!componentCategoriesEditWindowExists)
		{
			componentCategoriesEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainComponentWindowObj, '', 'index.php?fsaction=AdminComponentCategories.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}
	
	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;
		
		if(!componentCategoriesEditWindowExists)
		{
			componentCategoriesEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainComponentWindowObj, '', 'index.php?fsaction=AdminComponentCategories.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	/* delete handler */	  
	function onDelete(btn, ev)
	{
		var gridObj = Ext.getCmp('maingrid');
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
				
			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var codeList = '';
			var displayType = '';
		
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				codeList = codeList + selRecords[rec].data.code;
				displayType = displayType + selRecords[rec].data.islist;
				
				if (rec != selRecords.length - 1)
				{
					codeList = codeList + ',';
					displayType = displayType + ',';
				}	
			}
		
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
			paramArray['codelist'] = codeList;
			paramArray['displaytype'] = displayType;
		
			Ext.taopix.formPost(gMainComponentWindowObj, paramArray, 'index.php?fsaction=AdminComponentCategories.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);	
		}
	}

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = Ext.getCmp('maingrid');
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
	
	function onActivate(btn, ev)
	{ 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));	
		var active = 0; 

		switch (btn.id)
		{
			case 'categoriesActiveButton':
				active = 1;
				break;
			case 'categoriesInactiveButton':
				active = 0;
				break;
		}

		var gridObj = Ext.getCmp('maingrid');
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

		serverParams['codelist'] = codeList;
		serverParams['active'] = active;

		Ext.taopix.formPost(gMainComponentWindowObj, serverParams, 'index.php?fsaction=AdminComponentCategories.activate&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", updateImagaeServerGridCallback);	
	}
	
	function componentActivateCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var maingridObj = gComponentsDialogObj.findById('maingrid');
			var dataStore = maingridObj.store;
	
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
		}
	}

	function updateImagaeServerGridCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = Ext.getCmp('maingrid');
			var dataStore = gridObj.store;
	
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
			dataStore.reload();
		}
	}
	
	function componentHandler(btn, ev)
	{
		if(!componentsWindowExists)
		{
			componentsWindowExists = true;
			Ext.taopix.loadJavascript(gMainComponentWindowObj, '', 'index.php?fsaction=AdminComponents.initialize&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}
	
	
	function pricingModelRenderer(value, p, record)
	{
		
		className =  'class = "inactive"';
		
		if (record.data.status == 0)
		{
			className =  'class = "inactive"';
		}
		else
		{
			className =  'class = " "';
		}
		
		switch(value)
		{
			case '0':
				return '{/literal}<span '+className+">{#str_LabelNoPricing#}</span>{literal}";
			break;
			case '1':
				return '{/literal}<span '+className+">{#str_LabelPerOrder#}</span>{literal}";
			break;
			case '2':
				return '{/literal}<span '+className+">{#str_LabelPerLine#}</span>{literal}";
			break;
			case '3':
				return '{/literal}<span '+className+">{#str_LabelPerQty#}</span>{literal}";
			break;
			case '4':
				return '{/literal}<span '+className+">{#str_LabelPerPageQty#}</span>{literal}";
			break;
			case '5':
				return '{/literal}<span '+className+">{#str_LabelPerPageQty#}</span>{literal}";
			break;
			case '6':
				return '{/literal}<span '+className+">{#str_LabelPerCharacter#}</span>{literal}";
			break;
			case '7':
				return '{/literal}<span '+className+">{#str_LabelPerProductCmpQty#}</span>{literal}";
			break;
			case '8':
				return '{/literal}<span '+className+">{#str_LabelPerPageProductCmpQty#}</span>{literal}";
			break;
		}
	}
	
	function displayTypeRenderer(value, p, record)
	{
		className =  'class = "inactive"';
		
		if (record.data.status == 0)
		{
			className =  'class = "inactive"';
		}
		else
		{
			className =  'class = " "';
		}
		
		switch(value)
		{
			case '0':
				return '{/literal}<span '+className+">{#str_LabelCheckBox#}</span>{literal}";
			break;
			case '1':
				return '{/literal}<span '+className+">{#str_LabelList#}</span>{literal}";
			break;
		}
	}

	function componentTypeRenderer(value, p, record)	
	{
		switch(value)
		{
			case 'R':
				return "{/literal}{#str_LabelRootComponent#}{literal}";
			break;
			case 'L':
				return "{/literal}{#str_LabelComponentList#}{literal}";
			break;
			case 'S':
				return "{/literal}{#str_LabelComponentStructure#}{literal}";
			break;
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
			Ext.getCmp('hideInactiveCategoriesButton').setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');
		}
		else
		{
			Ext.getCmp('hideInactiveCategoriesButton').setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');			
		}

		// manually set the last options to allow reload to be passed hide inactive
		gridDataStore.lastOptions.params['hideInactive'] = hideInactive;

		gridDataStore.reload({params: gridDataStore.lastOptions.params});
	}

	function checkHideInactiveButton (pStore, pOptions)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveCategoriesButton');

		if (typeof hideInactiveButton !== 'undefined')
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
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					grid.editButton.enable();
					grid.componentsButton.enable();
				}
				else
				{
					grid.editButton.disable();
					grid.componentsButton.disable();
				}
				
				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
				}
                
                if (selectionCount == 1 || selectionCount > 1)
				{
                    var selectID = Ext.taopix.gridSelection2IDList(gMainComponentWindowObj.findById('maingrid'));
					var idList = selectID.split(',');
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('maingrid').store.getById(idList[i]);
                        
						{/literal}{if $optionms}{literal}
                            {/literal}{if $companyLogin}{literal}
                                if (record.data['companycode'] == '' && idList[i] > 0)
                                {
                                    grid.editButton.disable();
                                    grid.componentsButton.disable();
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
                                        grid.componentsButton.enable();
                                    }
                                    grid.activeButton.enable();
                                    grid.inactiveButton.enable();
                                    if (record.data['code'] != 'COVER' && record.data['code'] != "PAPER")
                                    {
                                        grid.deleteButton.enable();
                                    } else {
                                        grid.deleteButton.disable();
                                        break;
                                    }
                                }
                            {/literal}{else}{literal}
                                if (record.data['code'] == '' && record.data['companycode'] == '' && idList[i] > 0)
                                {
                                    grid.editButton.disable();
                                    grid.componentsButton.disable();
                                    grid.deleteButton.disable();
                                    grid.activeButton.disable();
                                    grid.inactiveButton.disable();
                                    break;
                                }
                                else
                                {
                                    grid.activeButton.enable();
                                    grid.inactiveButton.enable();
                                    if (record.data['code'] != 'COVER' && record.data['code'] != "PAPER")
                                    {
                                        grid.deleteButton.enable();
                                    } else {
                                        grid.deleteButton.disable();
                                        break;
                                    }
                                }
                            {/literal}{/if}{literal}
                        {/literal}{else}{literal}
                            if (record.data['code'] == '' && idList[i] > 0)
                            {
                                grid.editButton.disable();
                                grid.componentsButton.disable();
                                grid.deleteButton.disable();
                                grid.activeButton.disable();
                                grid.inactiveButton.disable();
                                break;
                            }
                            else
                            {
                                grid.activeButton.enable();
                                grid.inactiveButton.enable();
                                if (record.data['code'] != 'COVER' && record.data['code'] != "PAPER")
                                {
                                    grid.deleteButton.enable();
                                } else {
                                    grid.deleteButton.disable();
                                    break;
                                }
                            }
                        {/literal}{/if}{literal}
                    }
                }
                else
                {
                    grid.editButton.disable();
                    grid.componentsButton.disable();
                    grid.deleteButton.disable();
					grid.activeButton.disable();
					grid.inactiveButton.disable();
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
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponentCategories.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
		    {name: 'companycode', mapping: 1},
			{name: 'code', mapping: 2},
			{name: 'name', mapping: 3},
			{name: 'prompt', mapping: 4},
			{name: 'pricingmodel', mapping: 5},
			{name: 'islist', mapping: 6},
			{name: 'status', mapping: 7},
			{name: 'requirespagecount', mapping: 8},
			{name: 'decimalplaces', mapping: 9}
			])
		),
		listeners: {'beforeload' : checkHideInactiveButton},
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
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 170, dataIndex: 'code', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 300, dataIndex: 'name', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelPrompt#}{literal}", width: 300, dataIndex: 'prompt', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelPricingModel#}{literal}", renderer: pricingModelRenderer, width: 100, dataIndex: 'pricingmodel'},
			{header: "{/literal}{#str_LabelDisplayType#}{literal}", renderer: displayTypeRenderer, width: 100, dataIndex: 'islist'},
			{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 200, dataIndex: 'status', align: 'right'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_ComponentTitleComponentCategories#}{literal}" : "{/literal}{#str_LabelComponentCategory#}{literal}"]})' }),
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		autoExpandColumn: 6,
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
			},{ 
				id:'categoriesActiveButton',
				ref: '../activeButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'categoriesInactiveButton', 
				ref: '../inactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			},'-',
			{ 
                id: 'componentsButton',
				ref: '../componentsButton', 
				text: "{/literal}{#str_SectionTitleComponents#}{literal}", 
				iconCls: 'silk-wrench',
				handler: componentHandler,
				disabled:true
			}, '-'
			{/literal}{if $optionms}{literal}
			,
			new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			{/literal}{/if}{literal}
			,{xtype:'tbfill'},
			{
				id:'hideInactiveCategoriesButton',
				ref: '../hideInactiveCategoriesButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
				},
				{xtype: 'tbspacer', width: 10} 
		]
	});
	
	gridDataStoreObj.load({params: {hideInactive : 0}});

	gMainComponentWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_ComponentTitleComponentCategories#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainComponentWindowObj);
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
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
}

{/literal}