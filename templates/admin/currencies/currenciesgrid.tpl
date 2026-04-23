{literal}

function initialize(pParams)
{
	currencyEditWindowExists = false;

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

				var canDelete = true;

				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
				}

				if ((selectionCount > 0) && (canDelete))
				{
					grid.deleteButton.enable();
				}
				else
				{
					grid.deleteButton.disable();
				}
			}
		}
	});

	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminCurrencies.getGridData&ref={/literal}{$ref}{literal}', method: 'GET'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'name', mapping: 2},
			{name: 'iso', mapping: 3},
			{name: 'symbol', mapping: 4},
			{name: 'symbolfront', mapping: 5},
			{name: 'decimalplaces', mapping: 6},
			{name: 'exchangerate', mapping: 7}
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
			{header: "{/literal}{#str_LabelISOCode#}{literal}", width: 200, dataIndex: 'code'},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 300, dataIndex: 'name'},
			{header: "{/literal}{#str_LabelISONumber#}{literal}", width: 100, dataIndex: 'iso', align:'right'},
			{header: "{/literal}{#str_LabelSymbol#}{literal}", width: 100, dataIndex: 'symbol', align:'right'},
			{header: "{/literal}{#str_LabelSymbolAtFront#}{literal}", width: 100, renderer: logicColumnRenderer, dataIndex: 'symbolfront', align:'right'},
			{header: "{/literal}{#str_LabelDecimalPlaces#}{literal}", width: 100, dataIndex: 'decimalplaces', align:'right'},
			{header: "{/literal}{#str_LabelExchangeRate#}{literal}", width: 60, dataIndex: 'exchangerate', align:'right' }
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		autoExpandColumn: 7,
		selModel: gridCheckBoxSelectionModelObj,
		ctCls: 'grid',
		tbar: [
			{
				text: '{/literal}{#str_ButtonAdd#}{literal}',
				iconCls: 'silk-add',
				handler: onAdd
			}, '-',
			{
				ref: '../editButton',
				text: '{/literal}{#str_ButtonEdit#}{literal}',
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: '{/literal}{#str_ButtonDelete#}{literal}',
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			}, '-'
		]
	});

	gridDataStoreObj.load();

	/* add handler */
	function onAdd(btn, ev)
	{
		if (!currencyEditWindowExists)
		{
			currencyEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminCurrencies.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}

	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;

		if (!currencyEditWindowExists)
		{
			currencyEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminCurrencies.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	function logicColumnRenderer(value, p, record)
	{
		if (value == 0)
		{
			return '{/literal}{#str_LabelNo#}{literal}';
		}
		else
		{
			return '{/literal}{#str_LabelYes#}{literal}';
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

		dataStore.load();
		Ext.MessageBox.confirm('{/literal}{#str_LabelConfirmation#}{literal}', message, onDeleteResult);
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

			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminCurrencies.delete', '{/literal}{#str_MessageDeleting#}{literal}', onDeleteCallback);
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

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleCurrencies#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose();  accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
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
