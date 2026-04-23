{literal}
/* column rendering functions */
function columnRenderer(value, p, record)
{
	if (record.data.active == 1)
	{
		return value;
	}
	else
	{
		return '<i class="inactive">' + value + '</i>';
	}
}

function logicColumnRenderer(value, p, record, rowIndex, columnIndex)
{
	if (columnIndex == 4)
	{
		if (value == 0)
		{
			return columnRenderer("{/literal}{#str_LabelNo#}{literal}", p, record);
		}
		else
		{
			return columnRenderer("{/literal}{#str_LabelYes#}{literal}", p, record);
		}
	}
	else
	{
		return columnRenderer(value, p, record);
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


function initialize(pParams)
{
	var activeLabel_tx = "{/literal}{#str_LabelActive#}{literal}";
	var buttonCancel_tx = "{/literal}{#str_ButtonCancel#}{literal}";
	var editLabel_txt = "NEW--{/literal}{#str_TitleSiteAdministrationEdit#}{literal}";
	var buttonUpdate_tx ="{/literal}{#str_ButtonUpdate#}{literal}";

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
		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminSitesSitesAdmin.siteActivate', "{/literal}{#str_MessageUpdating#}{literal}", activateCallback);
	}

	function activeColumnRenderer(value, p, record)
	{
		if (value == 0)
		{
			return columnRenderer("{/literal}{#str_LabelInactive#}{literal}", p, record);
		}
		else
		{
			return columnRenderer("{/literal}{#str_LabelActive#}{literal}", p, record);
		}
	}

	function typeColumnRenderer(value, p, record)
	{
		if (value == 0)
		{
			return columnRenderer("{/literal}{#str_LabelProductionSite#}{literal}", p, record);
		}
		if (value == 1)
		{
			return columnRenderer("{/literal}{#str_LabelDistributionCentre#}{literal}", p, record);
		}
		if (value == 2)
		{
			if (record.data.productionsitekey == 1)
			{
				return columnRenderer("{/literal}{#str_LabelProductionSite#}<br>{#str_LabelStore#}{literal}", p, record);
			}
			else
			{
				return columnRenderer("{/literal}{#str_LabelStore#}{literal}", p, record);
			}
		}
	}

	function activateCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
			// reload to ensure hide inactive is functioning correctly
			dataStore.reload();
		}
	}

	/* add handler */
	function onAdd(btn, ev)
	{
		/* local parameters are passed to the javascript function */
		var localParams = new Object();
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['fsaction'] = 'AdminSitesSitesAdmin.addDisplay';
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php', serverParams, localParams, 'initialize', false);
	}

	/* edit handler */
	function onEdit(btn, ev)
	{
   		/* local parameters are passed to the javascript function */
		var localParams = new Object();

		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		Ext.taopix.loadJavascript(gMainWindowObj, "{/literal}{#str_ExtJsAlertLoading#}{literal}", 'index.php?fsaction=AdminSitesSitesAdmin.editDisplay', serverParams, localParams, 'initialize', false);
	}

	/* delete handler */
	function onDelete(btn, ev)
	{
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_ConfirmationDeleteStores#}{literal}", onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminSitesSitesAdmin.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);
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

			var alertMessage = pActionData.result.msg;

			if (pActionData.result.msg)
			{
				if (pActionData.result.alldeleted == '0')
				{
					icon = Ext.MessageBox.WARNING;
					if (pActionData.result.messagelist)
					{
						for (mes in pActionData.result.messagelist)
						{
							if (pActionData.result.messagelist[mes] != '')
							{
								alertMessage = alertMessage + '<br><br>'+pActionData.result.messagelist[mes];
							}
						}
					}
				}

				Ext.MessageBox.show({
					title: pActionData.result.title,
					msg: alertMessage,
					buttons: Ext.MessageBox.OK,
					icon: icon
				});
			}
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

				{/literal}{if $optionCFS}{literal}
				var canDelete = true;

				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
					for (var rec = 0; rec < selRecords.length; rec++)
					{
						if (selRecords[rec].data['productionsitekey'] == 1)
						{
							canDelete = false;
						}
					}
				}

				if ((selectionCount > 0) && (canDelete))
				{
					grid.deleteButton.enable();
				}
				else
				{
					grid.deleteButton.disable();
				}
				{/literal}{/if}{literal}
			}
		}
	});


	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminSitesSitesAdmin.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
				{name: 'companycode', mapping: 1},
				{name: 'sitecode', mapping: 2},
				{name: 'name', mapping: 3},
				{name: 'address', mapping: 4},
				{name: 'productionsitekey', mapping: 5},
				{name: 'sitetype', mapping: 6},
				{name: 'sitegroup', mapping: 7},
				{name: 'siteonline', mapping: 8},
				{name: 'active', mapping: 9}
			])
		),
		listeners: { beforeload: checkHideInactiveButton},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
	gridDataStoreObj.setDefaultSort('sitecode', 'asc');


	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: true,
			resizable: true,
			renderer: columnRenderer
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{/literal}{if $optionMS}{literal}
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 150, renderer: companyRenderer, dataIndex: 'companycode'},
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelSiteCode#}{literal}", width: 200, dataIndex: 'sitecode'},
			{header: "{/literal}{#str_LabelSiteName#}{literal}", width: 180, dataIndex: 'name'},
			{header: "{/literal}{#str_LabelAddress#}{literal}", width: 200, dataIndex: 'address', sortable: false},
			{/literal}{if $optionCFS}{literal}
				{header: "{/literal}{#str_LabelSiteType#}{literal}", width: 150, renderer: typeColumnRenderer, dataIndex: 'sitetype'},
				{header: "{/literal}{#str_LabelStoreGroup#}{literal}", width: 150, dataIndex: 'sitegroup'},
				{header: "{/literal}{#str_LabelSiteOnline#}{literal}", width: 80, renderer: logicColumnRenderer, dataIndex: 'siteonline', align: 'right'},
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelStatus#}{literal}", width: 80, renderer: activeColumnRenderer, dataIndex: 'active', align: 'right'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		ctCls: 'grid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		{/literal}{if !($optionMS) && $optionCFS}{literal}
			autoExpandColumn: 3,
		{/literal}{/if}{literal}
		{/literal}{if $optionMS && !($optionCFS)}{literal}
			autoExpandColumn: 4,
		{/literal}{/if}{literal}
		{/literal}{if $optionMS && $optionCFS}{literal}
			autoExpandColumn: 4,
		{/literal}{/if}{literal}
		{/literal}{if !($optionMS) && !($optionCFS)}{literal}
			autoExpandColumn: 3,
		{/literal}{/if}{literal}
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{/literal}{if $optionCFS}{literal}
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAdd
			}, '-',
			{/literal}{/if}{literal}
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}, '-',
			{/literal}{if $optionCFS}{literal}
			{
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			}, '-',
			{/literal}{/if}{literal}
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
            },
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
		],
        plugins: [
            new Ext.ux.grid.Search({
                iconCls: 'silk-zoom',
                minChars: 3,
                width: 200,
                autoFocus: true,
                disableIndexes:['companycode', 'siteonline', 'active', 'sitetype']
            })
        ],
		bbar: new Ext.PagingToolbar({
			store: gridDataStoreObj,
			displayInfo: true,
			pageSize: 100,
			listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh }
		})
	});

	gridDataStoreObj.load({
		params: {
			start: 0,
			limit: 100,
			fields: '',
			query: ''
		}
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_TitleSitesAdministration#}{literal}",
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

