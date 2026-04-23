{literal}

function initialize(pParams)
{
	policyEditWindowExists = false;

	// Configure listeners to enable/disable buttons in the grid based on how many items are selected.
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();

				// If there is one item selected enable the edit button.
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
					grid.deleteButton.enable();
					grid.activeButton.enable();
					grid.inactiveButton.enable();
				}
				else
				{
					grid.deleteButton.disable();
					grid.activeButton.disable();
					grid.inactiveButton.disable();
				}
			}
		}
	});

	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminDataRetentionAdmin.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
				idIndex: 0
			},
			Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'datecreated', mapping: 1},
				{name: 'code', mapping: 2},
				{name: 'name', mapping: 3},
				{name: 'active', mapping: 4},
				{name: 'error', mapping: 5}
			])
		)
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'name', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelStatus#}{literal}", dataIndex: 'active', width:80, renderer: columnRenderer, align:'right'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
		autoExpandColumn: 2,
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock: false,
		draggable: false,
		enableColumnHide: false,
		enableColumnMove: false,
		enableHdMenu: false,
		trackMouseOver: false,
		stripeRows: true,
		columnLines: true,
		selModel: gridCheckBoxSelectionModelObj,
		ctCls: 'grid',
		anchor: '100% 100%',
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
			}, '-',
			{
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			}, '-',
			{
				id: 'activeButton',
				ref: '../activeButton',
				text: "{/literal}{#str_LabelMakeActive#}{literal}",
				iconCls: 'silk-lightbulb',
				handler: onActivate,
				disabled: true
			}, '-',
        	{
				id: 'inactiveButton',
				ref: '../inactiveButton',
				text: "{/literal}{#str_LabelMakeInactive#}{literal}",
				iconCls: 'silk-lightbulb-off',
				handler: onActivate,
				disabled: true
			}
		]
	});

	function onActivate(btn, ev)
	{
		var gridObj = Ext.getCmp('maingrid');
		var maskText = "{/literal}{#str_Deactivating#}{literal}";
		var command = 0;
		if (btn.id == 'activeButton')
		{
			maskText = "{/literal}{#str_Activating#}{literal}";
			command = 1;
		}

		var paramArray = new Object();
		var selRecords = gridObj.getSelectionModel().getSelections();
		var ids = new Array();

		for (var rec = 0; rec < selRecords.length; rec++)
		{
			ids.push(selRecords[rec].data.id);
		}
		var iDList = ids.join(',');
		paramArray['idlist'] = iDList;
		paramArray['active'] = command;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminDataRetentionAdmin.setPolicyActiveStatus', maskText, onActivateCallback);
	};

	function onActivateCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			grid.store.reload();
		}
		else
		{
			Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		}
	};

	gridDataStoreObj.load();

	/* add handler */
	function onAdd(btn, ev)
	{
		if (!policyEditWindowExists)
		{
			policyEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminDataRetentionAdmin.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}

	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;

		if (!policyEditWindowExists)
		{
			policyEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminDataRetentionAdmin.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
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

		var message = "{/literal}{#str_DeleteConfirmation#}{literal}";
		message = message.replace("^0", codeList);

		dataStore.load();
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

			for (var rec = 0; rec < selRecords.length; rec++)
			{
				codeList = codeList + selRecords[rec].data.code;

				if (rec != selRecords.length - 1)
				{
					codeList = codeList + ',';
				}
			}

			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
			paramArray['codelist'] = codeList;

			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminDataRetentionAdmin.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);
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
			dataStore.load();
		}
	}

	function columnRenderer(value, p, record, rowIndex, colIndex, store)
	{
		if (colIndex == 3)
		{
			if (record.data.active == 0)
			{
				value = "{/literal}{#str_LabelInactive#}{literal}";
			}
			else
			{
				value = "{/literal}{#str_LabelActive#}{literal}";
			}
		}

		var className = '';
		if (record.data.active == 0)
		{
			className = ' class="inactive"';
		}

		return '<span' + className + '>' + value + '</span>';
	};


	var warningPanelItems = [];
	var warningPanelHeight = 0;
	var warningTemplate = new Ext.XTemplate('<tpl for="."><div class="warning-message">{error}</div></tpl>');

	{/literal}{if $taskSchedulerActive == 0}{literal}
		warningPanelItems.push({
			xtype: 'panel',
			style: { height: "27px" },
			flex: true,
			ctCls: "warning-bar",
			tpl: warningTemplate,
			data: [
				{error: "{/literal}{#str_MessageSchedulerInactive#}{literal}"}
			]
		});

		warningPanelHeight += 29;
	{/literal}{else}{literal}
		{/literal}{if $volumeAvailable == 0}{literal}
			warningPanelItems.push({
				xtype: 'panel',
				style: { height: "27px" },
				flex: true,
				ctCls: "warning-bar",
				tpl: warningTemplate,
				data: [
					{error: "{/literal}{#str_MessageNoArchiveVolume#}{literal}"}
				]
			});

			warningPanelHeight += 29;
		{/literal}{/if}{literal}

		{/literal}{if $purgeTasksActive == 0}{literal}
			warningPanelItems.push({
				xtype: 'panel',
				style: { height: "27px" },
				flex: true,
				ctCls: "warning-bar",
				tpl: warningTemplate,
				data: [
					{error: "{/literal}{#str_MessagePurgeTaskInactive#}{literal}"}
				]
			});

			warningPanelHeight += 29;
		{/literal}{/if}{literal}

		{/literal}{if $archiveTasksActive == 0}{literal}
			warningPanelItems.push({
				xtype: 'panel',
				style: { height: "27px" },
				flex: true,
				ctCls: "warning-bar",
				tpl: warningTemplate,
				data: [
					{error: "{/literal}{#str_MessageArchiveTaskInactive#}{literal}"}
				]
			});

			warningPanelHeight += 29;
		{/literal}{/if}{literal}
	{/literal}{/if}{literal}


	gWarningPanel = new Ext.Panel({
		id: 'warningpanel',
		height: warningPanelHeight,
		items: warningPanelItems
	});

	var pageItemsArray = [];
	if (warningPanelHeight != 0)
	{
		pageItemsArray.push(gWarningPanel);
	}
	pageItemsArray.push(grid);

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleManagementPolicies#}{literal}",
		items: pageItemsArray,
		layout: 'anchor',
		anchorSize: '100% 100%',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose();  accordianWindowInitialized = false;}, qtip: "{/literal}{#str_LabelCloseWindow#}{literal}" }],
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
