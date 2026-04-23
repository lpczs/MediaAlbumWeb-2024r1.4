{literal}

var gTPX_CONNECTOR_CONNECTED = "{/literal}{$TPX_CONNECTOR_CONNECTED}{literal}";

function initialize(pParams)
{
	var gridPageSize = 100;

	connectorsEditWindowExists = false;
	connectorsSyncProductsWindowExists = false;

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}

			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
		}
	};

	onCallbackSync = function(pUpdated, pTheForm, pActionData)
	{
		gridDataStoreObj.reload();
	}

	onCallbackDelete = function(pUpdated, pTheForm, pActionData)
	{
		gridDataStoreObj.reload();
	}

	onCallbackRebuild = function(pUpdated, pTheForm, pActionData)
	{
		gridDataStoreObj.reload();
	}

	var onAdd = function()
	{
		var paramArray = [];

		if (!connectorsEditWindowExists)
		{
			connectorsEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminConnectors.addDisplay', paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('connectorsGrid'));

		if (!connectorsEditWindowExists)
		{
			connectorsEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminConnectors.editDisplay', paramArray, '', 'initialize', true);
		}

	};

	var onDelete = function(btn, ev)
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				var gridObj = gMainWindowObj.findById('connectorsGrid');
				var selRecord = gridObj.selModel.getSelections()[0];

				paramArray['shopurl'] = selRecord.data.connectorurl;
				paramArray['id'] = Ext.taopix.gridSelection2IDList(gridObj);
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminConnectors.delete', "{/literal}{#str_MessageDeleting#}{literal}", onCallbackDelete);
			}
		};

		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteConfirmation#}{literal}", onDeleteConfirmed);
	};

	var onSync = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('connectorsGrid'));

		if (!connectorsSyncProductsWindowExists)
		{
			Ext.getCmp('MainWindow').el.mask("{/literal}{#str_MessageBuildingSyncWindow#}{literal}", 'x-mask-loading');
			connectorsSyncProductsWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminConnectors.syncProductsDisplay', paramArray, '', 'initialize', true);
		}

	};

	var onRebuild = function()
	{
		boxTitle = "{/literal}{#str_TitleError#}{literal}";
		boxMessage = "{/literal}{#str_TemplateRebuildFailiureMessage#}{literal}";

		var onRebuildConfirmed = function(btn)
		{
			if (btn == "yes") {
				Ext.MessageBox.wait("{/literal}{#str_WaitingTemplateRebuildMessage#}{literal}", "{/literal}{#str_WaitingTemplateRebuildTitle#}{literal}");

				var paramArray = {};
				var gridObj = gMainWindowObj.findById('connectorsGrid');
				var selRecord = gridObj.selModel.getSelections()[0];

				paramArray['shopurl'] = selRecord.data.connectorurl;
				paramArray['id'] = selRecord.data.recordid;
				paramArray['csrf_token'] = Ext.taopix.getCSRFToken();
				paramArray['ref'] = sessionId;

				Ext.Ajax.request({
					url: 'index.php?fsaction=AdminConnectors.rebuild',
					success: function(result, request)
					{
						if (parseJson(result.responseText).success)
						{
							boxTitle = "{/literal}{#str_TemplateRebuildSuccessTitle#}{literal}";
							boxMessage = "{/literal}{#str_TemplateRebuildSuccessMessage#}{literal}";
						}
						else
						{
							boxTitle = "{/literal}{#str_TitleError#}{literal}";
							boxMessage = "{/literal}{#str_TemplateRebuildFailiureMessage#}{literal}".replace("'^0'", parseJson(result.responseText).msg);
						}
						
						Ext.MessageBox.show({ title: boxTitle, msg: boxMessage, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO });
					},
					failure: function(result, request)
					{
						Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_TemplateRebuildFailiureMessage#}{literal}".replace("'^0'", result.status), buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
					},
					params: paramArray,
					method: 'POST'
				 });
				 
			}
		};

		var gridObj = gMainWindowObj.findById('connectorsGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push(selRecords[rec].data.connectorurllink);}

		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_RebuildConfirmation#}{literal}".replace("'^0'", codeList.join(', ')), onRebuildConfirmed);
	};

	var onThemePush = function()
	{

		var onThemePushConfirmed = function(btn)
		{
			if (btn == "yes") {
				Ext.MessageBox.wait("{/literal}{#str_WaitingThemePushMessage#}{literal}", "{/literal}{#str_WaitingThemePushMessage#}{literal}");

				var paramArray = {};
				var gridObj = gMainWindowObj.findById('connectorsGrid');
				var selRecord = gridObj.selModel.getSelections()[0];

				paramArray['shopurl'] = selRecord.data.connectorurl;
				paramArray['id'] = selRecord.data.recordid;
				paramArray['csrf_token'] = Ext.taopix.getCSRFToken();
				paramArray['ref'] = sessionId;

				Ext.Ajax.request({
					url: 'index.php?fsaction=AdminConnectors.themepush',
					success: function(result, request)
					{

						if (parseJson(result.responseText).success)
						{
							boxTitle = "{/literal}{#str_TemplateRebuildSuccessTitle#}{literal}";
							boxMessage = "{/literal}{#str_ThemePushSuccessMessage#}{literal}";
						}
						else
						{
							boxTitle = "{/literal}{#str_TitleError#}{literal}";
							boxMessage = "{/literal}{#str_ThemePushFailiureMessage#}{literal}".replace("'^0'", parseJson(result.responseText).msg);
						}

						Ext.MessageBox.show({ title: boxTitle, msg: boxMessage, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO });
					},
					failure: function(result, request)
					{
						Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ThemePushFailiureMessage#}{literal}".replace("'^0'", codeList.join(', ')), buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
					},
					params: paramArray,
					method: 'POST'
				 });
			}
		};

		var gridObj = gMainWindowObj.findById('connectorsGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push(selRecords[rec].data.connectorurllink);}

		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_ThemePushConfirmation#}{literal}", onThemePushConfirmed);
	};

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var style = '';
		if (colIndex == 4) 
		{
			style = 'color: #5BAE00;';

			if (record['data']['connectorstatus'] == 0)
			{
				style = 'color: #ff0000;';
			}
		}
		
		return '<span style="'+style+'">'+value+'</span>';
	};

	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminConnectors.list&ref=' + sessionId }),
		method:'POST',
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name:'recordid', mapping: 0},
				{name:'code', mapping: 1},
				{name:'company', mapping: 2},
				{name: 'foldername', mapping: 3},
				{name: 'connectorurllink', mapping: 4},
				{name: 'connectorstatustext', mapping: 5},
				{name: 'connectorstatus', mapping: 6},
				{name: 'brandid', mapping: 7},
				{name: 'connectorurl', mapping: 8}
			])
		),
		sortInfo:{field: 'foldername', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize }});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				var records = gridCheckBoxSelectionModelObj.getSelections();
				var hasDefault = false;
				for (var i = 0; i < records.length; i++)
				{
					if (records[i].data.code == '')
					{
						hasDefault = true;
						break;
					}
				}
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1)
					{
						gridObj.addButton.disable();
						gridObj.editButton.enable();
						gridObj.deleteButton.enable();

						var connectorstatus = records[0].data.connectorstatus;

						if (connectorstatus == gTPX_CONNECTOR_CONNECTED)
						{
							gridObj.syncButton.enable();
							gridObj.rebuildButton.enable();
							gridObj.themePushButton.enable();
						} 
						else 
						{
							gridObj.syncButton.disable();
							gridObj.rebuildButton.disable();
							gridObj.themePushButton.disable();
						}
					}
					else
					{
						gridObj.addButton.disable();
						gridObj.editButton.disable();
						gridObj.deleteButton.disable();
						gridObj.syncButton.disable();
						gridObj.rebuildButton.disable();
						gridObj.themePushButton.disable();
					}
				}
				else
				{
					gridObj.addButton.enable();
					gridObj.editButton.disable();
					gridObj.deleteButton.disable();
					gridObj.syncButton.disable();
					gridObj.rebuildButton.disable();
					gridObj.themePushButton.disable();
				}
			}
		}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
			{ id:'company', header: "{/literal}{#str_LabelCompanyName#}{literal}", dataIndex: 'company', hidden:true },
	    	{ id: 'foldername', header: "{/literal}{#str_LabelBrand#}{literal}", width:350, dataIndex: 'foldername', renderer: columnRenderer },
        	{ header: "{/literal}{#str_LabelConnectorURL#}{literal}", dataIndex: 'connectorurllink', width:450, renderer: columnRenderer},
			{ id: 'connectorstatustext', header: "{/literal}{#str_LabelConnectorStatus#}{literal}", dataIndex: 'connectorstatustext', width:450, renderer: columnRenderer, align: 'right'},
			{ id:'brandid', header: "", dataIndex: 'brandid', hidden:true }
        ]
	});

	gridObj = new Ext.grid.GridPanel({
   		id: 'connectorsGrid',
   		store: gridDataStoreObj,
    	selModel: gridCheckBoxSelectionModelObj,
    	cm: gridColumnModelObj,
    	stripeRows: true,
    	stateful: true,
    	enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
		autoExpandColumn: 'connectorstatustext',
		columnLines:true,
    	stateId: 'connectorsGrid',
    	ctCls: 'grid',
    	tbar: [	{
    				ref: '../addButton',
    				text: "{/literal}{#str_ButtonAdd#}{literal}",
    				iconCls: 'silk-add',
    				handler: onAdd,
    				disabled: false
    			}, '-',
        	    {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           		{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
				{ref: '../syncButton', text: "{/literal}{#str_ButtonSyncProductsPublish#}{literal}", iconCls: 'silk-book-go', handler: onSync, disabled: true, id:'syncButton' }, '-',
				{ref: '../rebuildButton', text: "{/literal}{#str_ButtonRebuildTemplates#}{literal}", iconCls: 'silk-layout-content', handler: onRebuild, disabled: true, id:'rebuildButton' }, '-',
				{ref: '../themePushButton', text: "{/literal}{#str_ButtonPushStockTheme#}{literal}", iconCls: 'silk-layout-content', handler: onThemePush, disabled: true, id:'themePushButton' }, ''   
      	   		
				{/literal}{if $optionMS == true}{literal}
				,{xtype:'tbfill'}
               	,{xtype: 'tbspacer', width: 10}
               	{/literal}{/if}{literal},
    	],
    	plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 230,
				autoFocus: true,
				disableIndexes: ['company','connectortype','connectorstatustext']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleConnectors#}{literal}",
		items: gridObj,
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
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
}

{/literal}
