function initialize(pParams)
{
	var actionName = 'AdminTaopixOnlineFontLists';
	var editWindow = false;
	var loadInProgress = false;

    function actionHandler(btn, ev)
    {
		var actions = {
			"{#str_ButtonDelete#}": 'deletefontlists'
		}

		var gridObj = Ext.getCmp('maingrid');
		var selectedList = [];

		gridObj.selModel.each(function(item) {
			selectedList.push(item.id);
		});

		if (actions.hasOwnProperty(btn.text) && 0 < selectedList.length)
		{
			Ext.MessageBox.confirm("{#str_LabelConfirmation#}", "{#str_MessageConfirmDelete#}", function(confBtn) {
				if ('yes' === confBtn)
				{
					var actionUrl = 'index.php?fsaction=' + actionName + '.' + actions[btn.text] + '&ref={$ref}';
					var fp = Ext.getCmp('maingrid');
					var postOptions = {
						selectedLists: selectedList
					};

					Ext.taopix.formPost(gMainWindowObj, postOptions, actionUrl, "{#str_MessageUpdating#}", function() {
						Ext.getCmp('maingrid').store.load();
					});
				}
			});
		}
    }

	function addEditHandler(btn, ev)
	{
		if (! editWindow)
		{
			var actionUrl = 'index.php?fsaction=' + actionName + '.formdisplay&ref={$ref}'
			if ("{#str_ButtonEdit#}" === btn.text)
			{
				var gridObj = Ext.getCmp('maingrid');
				var selRecords = gridObj.selModel.getSelections();
				var selectedList = selRecords[0].data.id;

				actionUrl += '&editItem=' + selectedList;
			}

			Ext.taopix.loadJavascript(gMainWindowObj, '', actionUrl, '', '', 'initialize', false);
		}
	}

	function generalColumnRenderer(value, p, record)
	{
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '<span '+className+'>'+value+'</span>';
		}
		else
		{
			return '<span class="">'+value+'</span>';
		}
	}

	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({
			url: 'index.php?fsaction=' + actionName + '.getgriddata&ref={$ref}'
		}),
		reader: new Ext.taopix.PagedArrayReader(
			{
				idIndex: 0
			},
			Ext.data.Record.create([
		    {
		    	name: 'id',
		    	mapping: 0
			},
			{
				name: 'name',
				mapping: 1
			}
			])
		)
	});

	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();

				if (selectionCount == 1)
				{
					grid.editButton.enable();
					grid.deleteButton.enable();
				}
				else
				{
					grid.editButton.disable();
				}

				if (0 === selectionCount)
				{
					grid.deleteButton.disable();
				}
			}
		}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{
				header: "{#str_LabelName#}",
				width: 200,
				dataIndex: 'name',
				renderer: generalColumnRenderer
			}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		autoExpandColumn: 1,
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
				ref: '../addButton',
				text: "{#str_ButtonAdd#}",
				iconCls: 'silk-add',
				handler: addEditHandler,
				disabled: false
			}, '-',
			{
				ref: '../editButton',
				text: "{#str_ButtonEdit#}",
				iconCls: 'silk-pencil',
				handler: addEditHandler,
				disabled: true
			}, '-',
			{
				ref: '../deleteButton',
				text: "{#str_ButtonDelete#}",
				iconCls: 'silk-delete',
				handler: actionHandler,
				disabled: true
			}
		]
	});

	gridDataStoreObj.load({
		callback: function(r, options, success) {
			if (grid.selModel !== null && grid.selModel.getCount() > 0)
			{
				grid.editButton.enable();
				grid.copyButton.enable();
				grid.deleteButton.enable();
			}
		}
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{#str_TitleFontLists#}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{
		    id: 'close',
		    handler: function(event, toolEl, panel) {
		        windowClose();
		        accordianWindowInitialized = false
		    },
		    qtip: "{#str_LabelCloseWindow#}"
        }],
		baseParams: {
            ref: "{$ref}"
        }
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